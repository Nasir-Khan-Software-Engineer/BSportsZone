<?php
namespace App\Services\Pos;

use App\Repositories\Pos\IPosRepository;
use App\Services\Product\IProductService;

class PosService implements IPosService{

    public function __construct(IPosRepository $iPosRepository,
                                IProductService $iProductService){
        $this->posRepository = $iPosRepository;
        $this->productService = $iProductService;
    }

    public function recentProducts($posId, $shopId, $categoryId, $brandId){
        return $this->posRepository->recentProducts($posId, $shopId, $categoryId, $brandId);
    }

    public function getPosPageProducts($posId){
        $ids = $this->productService->getTopSellingProductIds($posId);
        if($ids && count($ids) > 0){
            return $this->productService->getProductByIds($ids);
        }

        return $this->productService->getRecentProducts($posId, 1, 0, 0);
    }
}

