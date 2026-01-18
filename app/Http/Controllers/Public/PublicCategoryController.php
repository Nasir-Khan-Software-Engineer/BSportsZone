<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\Product\IPublicProductService;
use Illuminate\Http\Request;

class PublicCategoryController extends Controller
{
    protected $publicProductService;

    public function __construct(IPublicProductService $publicProductService)
    {
        $this->publicProductService = $publicProductService;
    }

    public function index(Request $request, $slug, $page = null)
    {
        // Find the category by slug
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Get page from route parameter or query string, default to 1
        $page = $page ?? $request->query('page', 1);
        $page = max(1, (int) $page);
        
        // Get published products for this category with pagination (16 per page)
        $products = Product::where('is_published', true)
            ->where('type', 'Product') // Only show products, not services
            ->whereHas('categories', function($query) use ($category) {
                $query->where('category_id', $category->id);
            })
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
        
        return view('public.page.category', compact('products', 'currentPage', 'lastPage', 'category'));
    }
}
