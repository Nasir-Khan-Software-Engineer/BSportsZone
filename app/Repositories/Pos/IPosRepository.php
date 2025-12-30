<?php
namespace App\Repositories\Pos;

interface IPosRepository{

    public function recentServices($posId, $shopId, $categoryId, $brandId);
    public function getTopSellingServices($posId);
}

?>
