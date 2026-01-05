<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\Sales_items;
use DB;
use App\Repositories\Product\IProductRepository;

class ProductService implements IProductService
{
    private $productRepository;
    public function __construct(IProductRepository $productRepository){
        $this->productRepository = $productRepository;
    }
    
    public function searchProduct($posId, $serviceName, $categoryId){
        return $this->productRepository->searchProduct($posId, $serviceName, $categoryId);
    }

    public function getProductByIds($productIds)
    {
        return Product::with(['variations' => function ($q) {
                $q->active();
            }])
            ->select('id', 'name', 'code', 'POSID', 'image', 'type')
            ->where('type', 'Product')
            ->whereIn('id', $productIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $productIds) . ')')
            ->get()
            ->flatMap(function ($product) {
                return $product->variations->map(function ($variation) use ($product) {
                    return (object) [
                        'id'           => $product->id,
                        'code'         => $product->code,
                        'variation_id' => $variation->id,
                        'name'         => $product->name,
                        'type'         => $product->type,
                        'posid'        => $product->POSID,
                        'image'        => $product->image,
                        'stock'        => $variation->stock ?? 0,
                        'price'        => $variation->selling_price ?? 0,
                        'tagline'      => $variation->tagline,
                    ];
                });
            })
            ->values();
    }



    public function getTopSellingProductIds($posId){
        $productGroupBy = Sales_items::select('product_id as id', DB::raw('COUNT(product_id) as qty'))
        ->where('POSID', $posId)
        ->where('type', 'Product')
        ->groupBy('product_id')
        ->orderByDesc('qty')
        ->limit(32)
        ->get();

        return $productGroupBy->pluck('id')->toArray();
    }

    public function getRecentProducts($posId)
    {
        return Product::with(['variations' => function ($q) {
                $q->active();
            }])
            ->select(
                'id',
                'name',
                'code',
                'POSID',
                'image',
                'type',
                'updated_at'
            )
            ->where('type', 'Product')
            ->where('POSID', $posId)
            ->orderBy('updated_at', 'desc')
            ->limit(30)
            ->get()
            ->flatMap(function ($product) {
                return $product->variations->map(function ($variation) use ($product) {
                    return (object) [
                        'id'           => $product->id,
                        'code'         => $product->code,
                        'variation_id' => $variation->id,
                        'name'         => $product->name,
                        'type'         => $product->type,
                        'posid'        => $product->POSID,
                        'image'        => $product->image,

                        // variation data
                        'stock'        => $variation->stock ?? 0,
                        'price'        => $variation->selling_price ?? 0,
                        'tagline'      => $variation->tagline,
                    ];
                });
            })
            ->values();
    }


    public function getTopSellingProducts($posId){
        $ids = $this->getTopSellingProductIds($posId);
        if($ids && count($ids) > 0){
            return $this->getProductByIds($ids);
        }

        return $this->getRecentProducts($posId);
    }
}
