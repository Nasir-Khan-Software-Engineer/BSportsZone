<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Sales;
use App\Models\Sales_items;
use App\Models\Product;
use App\Models\Variation;
use App\Models\Accountinfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\Product\IPublicProductService;

class PublicIndexController extends Controller
{

    public function __construct(IPublicProductService $publicProductService)
    {
        $this->publicProductService = $publicProductService;
    }


    public function index()
    {

    // logout code 

        // Auth::logout();
              //  Session::flush();

        $homeProducts = Product::where('is_published', true)
            ->where('type', 'Product')
            ->where('is_home', true)
            ->with('variations')
            ->select([
                'id',
                'name',
                'slug',
                'price',
                'image',
                'discount_type',
                'discount_value',
                'seo_keyword',
                'seo_description',
                'description'
            ])->get();

        $homeProducts = $this->publicProductService->formatProducts($homeProducts);


        return view('public.page.index', compact('homeProducts'));
    }

    public function checkout()
    {
        return view('public.checkout');
    }

    public function thanks($order)
    {
        return view('public.thanks', compact('order'));
    }

    public function placeOrder(Request $request)
    {
        // Validate form data
        $validator = Validator::make($request->all(), [
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'required|string|max:20',
            'deliveryArea' => 'required|string|in:inside dhaka,outside dhaka',
            'customerAddress' => 'required|string',
            'cartData' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cartData = json_decode($request->cartData, true);
        $cartData = $cartData['items'];
        
        if (!is_array($cartData) || empty($cartData)) {
            return redirect()->back()
                ->withErrors(['cartData' => 'Cart is empty'])
                ->withInput();
        }

        // Default POSID for public orders
        $POSID = 1;

        DB::beginTransaction();
        try {
            // Create or find customer
            $customer = Customer::firstOrCreate(
                [
                    'phone1' => $request->customerPhone,
                    'POSID' => $POSID
                ],
                [
                    'name' => $request->customerName,
                    'gender' => 'M', // Default gender
                    'address' => $request->customerAddress,
                    'source' => 'online',
                    'isActive' => true,
                    'type' => 'General',
                    'hasLoyalty' => 'No',
                ]
            );

            // Update customer information
            $customer->source = 'online';
            $customer->name = $request->customerName;
            $customer->address = $request->customerAddress;
            $customer->save();

            // Get account info for invoice prefix
            $invoicePrefix = "ORD";

            // Get all product and variation IDs from cart
            $productIds = collect($cartData)->pluck('id')->unique()->toArray();
            $variationIds = collect($cartData)->pluck('variation_id')->unique()->toArray();

            // Fetch products and variations from database
            $products = Product::whereIn('id', $productIds)
                ->where('POSID', $POSID)
                ->get()
                ->keyBy('id');

            $variations = Variation::whereIn('id', $variationIds)
                ->where('POSID', $POSID)
                ->get()
                ->keyBy('id');

            // Recalculate total amount and prepare sales items
            $totalAmount = 0;
            $salesItems = [];

            foreach ($cartData as $cartItem) {
                $productId = $cartItem['id'];
                $variationId = $cartItem['variation_id'];
                $quantity = (int) ($cartItem['quantity'] ?? 1);

                // Get product and variation from database
                $product = $products->get($productId);
                $variation = $variations->get($variationId);

                if (!$product || !$variation || $variation->product_id != $product->id) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withErrors(['cartData' => 'Invalid product or variation in cart'])
                        ->withInput();
                }

                // Get selling price and discount from database
                $sellingPrice = (float) $variation->selling_price;
                $discountType = $variation->discount_type ?? 'fixed';
                $discountValue = (float) ($variation->discount_value ?? 0);

                // Calculate price after discount
                $priceAfterDiscount = $sellingPrice;
                if ($discountType === 'percentage') {
                    $priceAfterDiscount = $sellingPrice - ($sellingPrice * $discountValue / 100);
                } else {
                    $priceAfterDiscount = $sellingPrice - $discountValue;
                }

                // Calculate item total
                $itemTotal = $priceAfterDiscount * $quantity;
                $totalAmount += $itemTotal;

                // Prepare sales item
                $salesItems[] = [
                    'POSID' => $POSID,
                    'sales_id' => null, // Will be set after sales is created
                    'product_id' => $productId,
                    'variation_id' => $variationId,
                    'variant_tagline' => $variation->tagline,
                    'type' => 'Product',
                    'product_price' => 0, // cost price from purchases
                    'selling_price' => $sellingPrice, // variation oginal selling price
                    'quantity' => $quantity,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Calculate delivery charge
            $deliveryCharge = 0;
            if ($request->deliveryArea === 'outside dhaka') {
                $deliveryCharge = 140;
            } else if ($request->deliveryArea === 'inside dhaka') {
                $deliveryCharge = 70;
            }

            $totalPayableAmount = $totalAmount + $deliveryCharge;

            // Create sales record
            $sales = new Sales();
            $sales->POSID = $POSID;
            $sales->invoice_code = $invoicePrefix . '-' . date('YmdHis');
            $sales->customerId = $customer->id;
            $sales->total_amount = $totalAmount;
            $sales->discount_type = 'fixed';
            $sales->discount_value = 0;
            $sales->discount_amount = 0;
            $sales->total_payable_amount = $totalPayableAmount;
            $sales->sales_from = 'online';
            $sales->sale_status = 'pending';
            $sales->payment_status = 'pending';
            $sales->note = 'Cash on delivery';
            $sales->delivery_area = $request->deliveryArea;
            $sales->shipping_address = $request->customerAddress;
            $sales->adjustmentAmt = 0;
            // For public orders, we don't have a user, so set to null or default user
            $sales->created_by = 1; // Default user ID
            $sales->updated_by = 1; // Default user ID
            $sales->save();

            // Update sales_id in sales items and insert them
            foreach ($salesItems as &$item) {
                $item['sales_id'] = $sales->id;
            }

            Sales_items::insert($salesItems);

            DB::commit();

            // Redirect to thanks page with invoice code and order_placed query parameter
            $thanksUrl = route('order.thanks', ['order' => $sales->invoice_code]) . '?order_placed=true';
            return redirect($thanksUrl);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to place order. Please try again.'])
                ->withInput();
        }
    }
}
