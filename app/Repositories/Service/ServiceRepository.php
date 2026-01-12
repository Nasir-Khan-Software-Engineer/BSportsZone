<?php

namespace App\Repositories\Service;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
class ServiceRepository implements IServiceRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function searchService($posId, $serviceName, $categoryId){
        $services = Product::select(
                'products.id',
                'products.name',
                'products.POSID',
                'code',
                'products.price',
                'products.image',
                'products.staff_id',
                'products.type',
                DB::raw('0 as stock'),
                DB::raw('0 as variation_id'),
                DB::raw('"" as tagline'),
                'products.discount_type',
                'products.discount_value'
            )
            ->with('TodaysStaff:id,name')
            ->where('products.POSID', $posId)
            ->where('type', 'Service')
            // search by name or code
            ->when($serviceName, function ($query, $serviceName) {
                $query->where(function ($q) use ($serviceName) {
                    $q->where('products.name', 'like', "%{$serviceName}%")
                    ->orWhere('code', 'like', "%{$serviceName}%");
                });
            })

            // filter by category if set and not 0
            ->when(!empty($categoryId) && $categoryId != 0, function ($query) use ($categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('category.id', $categoryId);
                });
            })

            ->orderBy('products.updated_at', 'desc')
            ->limit(30)
            ->get();
        return $services;
    }
}
