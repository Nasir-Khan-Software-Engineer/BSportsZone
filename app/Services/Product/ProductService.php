<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\Purchase_items;
use DB;

class ProductService implements IProductService {

    public function __construct(){
    }

    public function getProductByIds($productIds)
    {
        return Product::select(
                'products.id',
                'products.name',
                'products.posid',
                'products.code',
                'products.price',
                'products.image',
                'products.beautician_id'
            )
            ->with([
                'TodaysBeautician:id,name'
            ])
            ->whereIn('products.id', $productIds)
            ->orderByRaw('FIELD(products.id, ' . implode(',', $productIds) . ')')
            ->get();
    }


    public function getTopSellingProductIds($posId){
        $productGroupBy = Purchase_items::select('product_id as id', DB::raw('COUNT(product_id) as qty'))
        ->where('posid', $posId)
        ->groupBy('product_id')
        ->orderByDesc('qty')
        ->limit(32)
        ->get();

        return $productGroupBy->pluck('id')->toArray();
    }

    public function getRecentProducts($posId, $shopId, $categoryId, $brandId)
    {
        return Product::select(
                'products.id',
                'products.name',
                'products.posid',
                'products.code',
                'products.price',
                'products.image',
                'products.beautician_id'
            )
            ->with([
                'TodaysBeautician:id,name'
            ])
            ->where('products.posid', $posId)
            ->orderBy('products.updated_at', 'desc')
            ->limit(30)
            ->get();
    }

}
