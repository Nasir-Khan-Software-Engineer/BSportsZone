<?php

namespace App\Repositories\Product;

interface IProductRepository
{
        public function searchProduct($posId, $serviceName, $categoryId);

}
