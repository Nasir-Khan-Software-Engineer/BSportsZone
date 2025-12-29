<?php
namespace App\Services\Pos;

use App\Repositories\Pos\IPosRepository;

interface IPosService{
    public function recentProducts($posId, $shopId, $categoryId, $brandId);
    public function getPosPageProducts($posId);
}

