<?php

namespace App\Repositories\Pos;

use App\Models\Product;
use App\Repositories\Pos\IPosRepository;
use App\Models\Sales_items;
use DB;

class PosRepository implements IPosRepository {

    public function getTopSellingServices($posId){

        $serviceGroupBy = Sales_items::select('product_id as id', DB::raw('COUNT(product_id) as qty'))
        ->where('POSID', $posId)
        ->where('type', 'Service')
        ->groupBy('product_id')
        ->orderByDesc('qty')
        ->limit(32)
        ->get();

        $serviceIds = $serviceGroupBy->pluck('id')->toArray();

        return Product::select('products.id', 'products.name', 'products.POSID', 'code',  'products.price', 'products.image')
            ->where('products.POSID', $posId)
            ->where('type', 'Service')
            ->whereIn('products.id', $serviceIds)
            ->orderByRaw('FIELD(products.id, ' . implode(',', $serviceIds) . ')')
            ->get();
    }
}

?>
