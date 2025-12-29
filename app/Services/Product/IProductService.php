<?php
namespace App\Services\Product;

interface IProductService {
    public function getProductByIds($productIds);
    public function getTopSellingProductIds($posId);
    public function getRecentProducts($posId, $shopId, $categoryId, $brandId);
}
