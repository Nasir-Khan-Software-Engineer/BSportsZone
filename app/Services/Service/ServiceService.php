<?php

namespace App\Services\Service;

use App\Models\Product;
use App\Models\Sales_items;
use DB;

class ServiceService implements IServiceService {

    public function __construct(){
    }

    public function getServiceByIds($serviceIds)
    {
        return Product::select(
                'products.id',
                'products.name',
                'products.POSID',
                'products.code',
                'products.price',
                'products.image',
                'products.staff_id'
            )
            ->with([
                'TodaysStaff:id,name'
            ])
            ->where('type', 'Service')
            ->whereIn('products.id', $serviceIds)
            ->orderByRaw('FIELD(products.id, ' . implode(',', $serviceIds) . ')')
            ->get();
    }


    public function getTopSellingServiceIds($posId){
        $serviceGroupBy = Sales_items::select('product_id as id', DB::raw('COUNT(product_id) as qty'))
        ->where('POSID', $posId)
        ->groupBy('product_id')
        ->orderByDesc('qty')
        ->limit(32)
        ->get();

        return $serviceGroupBy->pluck('id')->toArray();
    }

    public function getRecentServices($posId, $shopId, $categoryId, $brandId)
    {
        return Product::select(
                'products.id',
                'products.name',
                'products.POSID',
                'products.code',
                'products.price',
                'products.image',
                'products.staff_id'
            )
            ->with([
                'TodaysStaff:id,name'
            ])
            ->where('type', 'Service')
            ->where('products.POSID', $posId)
            ->orderBy('products.updated_at', 'desc')
            ->limit(30)
            ->get();
    }

}
