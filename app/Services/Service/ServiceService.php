<?php

namespace App\Services\Service;

use App\Models\Product;
use App\Models\Sales_items;
use DB;
use App\Repositories\Service\IServiceRepository;

class ServiceService implements IServiceService {

    public function __construct(IServiceRepository $serviceRepository){
        $this->serviceRepository = $serviceRepository;
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
                'products.staff_id',
                'products.type'
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
        ->where('type', 'Service')
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
                'products.staff_id',
                'products.type'
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

    public function searchService($posId, $serviceName, $categoryId){
        return $this->serviceRepository->searchService($posId, $serviceName, $categoryId);
    }

    public function getTopSellingServices($posId){
        $ids = $this->getTopSellingServiceIds($posId);
        if($ids && count($ids) > 0){
            return $this->getServiceByIds($ids);
        }

        return $this->getRecentServices($posId, 1, 0, 0);
    }

}
