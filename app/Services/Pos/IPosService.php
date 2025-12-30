<?php
namespace App\Services\Pos;

use App\Repositories\Pos\IPosRepository;

interface IPosService{
    public function recentServices($posId, $shopId, $categoryId, $brandId);
    public function getPosPageServices($posId);
}

