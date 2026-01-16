<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\IPublicProductService;
use Illuminate\Http\Request;

class PublicProductController extends Controller
{
    protected $publicProductService;

    public function __construct(IPublicProductService $publicProductService)
    {
        $this->publicProductService = $publicProductService;
    }
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


        $products = $this->publicProductService->formatProducts($products);
        
        // Get pagination info for custom URL generation
        $currentPage = $products->currentPage();
        $lastPage = $products->lastPage();
        
        return view('public.page.shop', compact('products', 'currentPage', 'lastPage'));
    }

    public function product($slug)
    {
        $product = Product::with('images', 'variations', 'relatedProducts')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->where('type', 'Product')
            ->firstOrFail();

            $relatedProducts = $this->publicProductService->formatProducts($product->relatedProducts->where('is_published', true)->where('type', 'Product'));
  
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
            

        return view('public.product.single', compact('product', 'priceAfterDiscount', 'variations', 'defaultVariation', 'relatedProducts'));
    }


}
