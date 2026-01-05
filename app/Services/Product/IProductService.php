<?php

namespace App\Services\Product;

interface IProductService
{
    public function searchProduct($posId, $serviceName, $categoryId);
    public function getProductByIds($productIds);
    public function getTopSellingProductIds($posId);
    public function getRecentProducts($posId);
    public function getTopSellingProducts($posId);
}
