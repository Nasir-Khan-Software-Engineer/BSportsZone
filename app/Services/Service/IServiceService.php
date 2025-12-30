<?php
namespace App\Services\Service;

interface IServiceService {
    public function getServiceByIds($serviceIds);
    public function getTopSellingServiceIds($posId);
    public function getRecentServices($posId, $shopId, $categoryId, $brandId);
}
