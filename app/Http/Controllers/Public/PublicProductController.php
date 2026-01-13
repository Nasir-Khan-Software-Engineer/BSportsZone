<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicProductController extends Controller
{
    public function index(Request $request, $page = null)
    {
        // Get page from route parameter or query string, default to 1
        $page = $page ?? $request->query('page', 1);
        $page = max(1, (int) $page);
        
        // Get published products with pagination (16 per page)
        $products = Product::where('is_published', true)
            ->where('type', 'Product') // Only show products, not services
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
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(16, ['*'], 'page', $page);



        foreach ($products as $product) {
            $product->short_name = Str::limit($product->name, 30);
            // 1️⃣ Calculate product price after discount
            $price = $product->price;

            if ($product->discount_type && $product->discount_value) {
                if ($product->discount_type === 'percentage') {
                    $price -= ($product->price * $product->discount_value / 100);
                } else {
                    $price -= $product->discount_value;
                }
            }

            $product->price_after_discount = round(max(0, $price), 2);

            // 2️⃣ Get default variation
            $defaultVariation = $product->variations->where('is_default', 1)->first();

            if ($defaultVariation) {

                // 3️⃣ Calculate default variation price after discount
                $vPrice = $defaultVariation->selling_price;

                if ($defaultVariation->discount_type && $defaultVariation->discount_value) {
                    if ($defaultVariation->discount_type === 'percentage') {
                        $vPrice -= ($defaultVariation->selling_price * $defaultVariation->discount_value / 100);
                    } else {
                        $vPrice -= $defaultVariation->discount_value;
                    }
                }

                $defaultVariation->price_after_discount = round(max(0, $vPrice), 2);

                // 4️⃣ Attach variation info to product
                $product->default_variation_id = $defaultVariation->id;
                $product->default_variation_tagline = $defaultVariation->tagline;
                $product->default_variation_selling_price = $defaultVariation->selling_price;
                $product->default_variation_discount_type = $defaultVariation->discount_type;
                $product->default_variation_discount_value = $defaultVariation->discount_value;
                $product->default_variation_price_after_discount = $defaultVariation->price_after_discount;


            } else {
                // Fallback if no default variation exists
                $product->default_variation_id = null;
                $product->default_variation_tagline = null;
                $product->default_variation_selling_price = null;
                $product->default_variation_discount_type = null;
                $product->default_variation_discount_value = null;
                $product->default_variation_price_after_discount = null;
            }
        }

        
        // Get pagination info for custom URL generation
        $currentPage = $products->currentPage();
        $lastPage = $products->lastPage();
        
        return view('public.page.shop', compact('products', 'currentPage', 'lastPage'));
    }

    public function product($slug)
    {
        $product = Product::with('images', 'variations')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->where('type', 'Product')
            ->firstOrFail();

  
        // Calculate final price for product
        $priceAfterDiscount = $product->price;

        if ($product->discount_type && $product->discount_value) {
            if ($product->discount_type === 'percentage') {
                $priceAfterDiscount = $product->price - ($product->price * $product->discount_value / 100);
            } else {
                $priceAfterDiscount = $product->price - $product->discount_value;
            }

            $priceAfterDiscount = max(0, $priceAfterDiscount); // prevent negative
        }

        // Get only active variations
        $variations = $product->variations->where('status', 'active');
        $defaultVariation = null;
        // Calculate discounted price for each variation
        foreach ($variations as $variation) {

            $variationPrice = $variation->selling_price;

            if ($variation->discount_type && $variation->discount_value) {
                if ($variation->discount_type === 'percentage') {
                    $variationPrice -= ($variation->selling_price * $variation->discount_value / 100);
                } else {
                    $variationPrice -= $variation->discount_value;
                }

                $variationPrice = max(0, $variationPrice);
            }

            // Attach discounted price dynamically
            $variation->price_after_discount = round($variationPrice, 2);

            if ($variation->is_default) {
                $defaultVariation = $variation;
            }
        }
            

        return view('public.product.single', compact('product', 'priceAfterDiscount', 'variations', 'defaultVariation'));
    }

}
