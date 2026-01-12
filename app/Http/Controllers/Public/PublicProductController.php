<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

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
            ->with(['defaultImage.mediaImage'])
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

        // Set first variation as default if variations exist and no default is set
        if ($product->variations->count() > 0) {
            $hasDefault = $product->variations->where('is_default', true)->count() > 0;
            
            if (!$hasDefault) {
                // Set first variation as default
                $firstVariation = $product->variations->first();
                $firstVariation->is_default = true;
                $firstVariation->save();
                
                // Refresh the relationship to get updated data
                $product->load('variations');
            }
        }

        // Calculate final price for product (fallback if no variations)
        $finalPrice = $product->price;

        if ($product->discount_type && $product->discount_value) {
            if ($product->discount_type === 'percentage') {
                $finalPrice = $product->price - ($product->price * $product->discount_value / 100);
            } else {
                $finalPrice = $product->price - $product->discount_value;
            }

            $finalPrice = max(0, $finalPrice); // prevent negative
        }

        // Calculate final price for default variation if exists
        $defaultVariation = $product->variations->where('is_default', true)->first();
        $defaultVariationPrice = $finalPrice;
        
        if ($defaultVariation) {
            $defaultVariationPrice = $defaultVariation->selling_price;
            
            if ($defaultVariation->discount_type && $defaultVariation->discount_value) {
                if ($defaultVariation->discount_type === 'percentage') {
                    $defaultVariationPrice = $defaultVariation->selling_price - ($defaultVariation->selling_price * $defaultVariation->discount_value / 100);
                } else {
                    $defaultVariationPrice = $defaultVariation->selling_price - $defaultVariation->discount_value;
                }
                
                $defaultVariationPrice = max(0, $defaultVariationPrice); // prevent negative
            }
        }

        return view('public.product.single', compact('product', 'finalPrice', 'defaultVariation', 'defaultVariationPrice'));
    }

}
