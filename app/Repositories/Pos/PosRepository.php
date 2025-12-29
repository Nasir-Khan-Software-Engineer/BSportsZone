<?php

namespace App\Repositories\Pos;

use App\Models\Product;
use App\Repositories\Pos\IPosRepository;
use App\Models\Purchase_items;
use DB;

class PosRepository implements IPosRepository {

    public function recentProducts($posId, $shopId, $categoryId, $brandId){
        return Product::select('products.id', 'products.name', 'products.posid', 'code',  'products.price', 'products.image')
            ->where('products.posid', $posId)->orderBy('products.updated_at', 'desc')->limit(30)->get();
    }

    public function getTopSellingProduct($posId){
        //$result = DB::raw('select productid, count(productid) as qty from purchases_items where posid = $posid group by productid order by qty desc limit 10')->get();

        $productGroupBy = Purchase_items::select('product_id as id', DB::raw('COUNT(product_id) as qty'))
        ->where('posid', $posId)
        ->groupBy('product_id')
        ->orderByDesc('qty')
        ->limit(32)
        ->get();

        $productIds = $productGroupBy->pluck('id')->toArray();

        return Product::select('products.id', 'products.name', 'products.posid', 'code',  'products.price', 'products.image')
            ->where('products.posid', $posId)
            ->whereIn('products.id', $productIds)
            ->orderByRaw('FIELD(products.id, ' . implode(',', $productIds) . ')')
            ->get();
    }
}

?>
