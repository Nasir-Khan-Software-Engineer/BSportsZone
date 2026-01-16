<?php

namespace App\Services\Product;

interface IPublicProductService
{
    /**
     * Format products for public display
     * 
     * @param mixed $products Collection or Paginator of products
     * @return mixed Formatted products
     */
    public function formatProducts($products);
}
