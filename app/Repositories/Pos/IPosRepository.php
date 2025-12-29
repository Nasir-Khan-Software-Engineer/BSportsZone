<?php
namespace App\Repositories\Pos;

interface IPosRepository{

    public function recentProducts($posId, $shopId, $categoryId, $brandId);
    public function getTopSellingProduct($posId);
}

?>
