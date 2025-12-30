<?php

namespace App\Repositories\Pos;

use App\Models\Product;
use App\Repositories\Pos\IPosRepository;
use App\Models\Sales_items;
use DB;

class PosRepository implements IPosRepository {

    public function recentServices($posId, $shopId, $categoryId, $brandId){
        return Product::select('products.id', 'products.name', 'products.posid', 'code',  'products.price', 'products.image')
            ->where('products.posid', $posId)->where('type', 'Service')->orderBy('products.updated_at', 'desc')->limit(30)->get();
    }

    public function getTopSellingServices($posId){
        //$result = DB::raw('select productid, count(productid) as qty from Sales_items where posid = $posid group by productid order by qty desc limit 10')->get();

        $serviceGroupBy = Sales_items::select('product_id as id', DB::raw('COUNT(product_id) as qty'))
        ->where('posid', $posId)
        ->groupBy('product_id')
        ->orderByDesc('qty')
        ->limit(32)
        ->get();

        $serviceIds = $serviceGroupBy->pluck('id')->toArray();

        return Product::select('products.id', 'products.name', 'products.posid', 'code',  'products.price', 'products.image')
            ->where('products.posid', $posId)
            ->where('type', 'Service')
            ->whereIn('products.id', $serviceIds)
            ->orderByRaw('FIELD(products.id, ' . implode(',', $serviceIds) . ')')
            ->get();
    }
}

?>
