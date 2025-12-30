<?php
namespace App\Services\Pos;

use App\Repositories\Pos\IPosRepository;
use App\Services\Service\IServiceService;

class PosService implements IPosService{

    public function __construct(IPosRepository $iPosRepository,
                                IServiceService $iServiceService){
        $this->posRepository = $iPosRepository;
        $this->serviceService = $iServiceService;
    }

    public function recentServices($posId, $shopId, $categoryId, $brandId){
        return $this->posRepository->recentServices($posId, $shopId, $categoryId, $brandId);
    }

    public function getPosPageServices($posId){
        $ids = $this->serviceService->getTopSellingServiceIds($posId);
        if($ids && count($ids) > 0){
            return $this->serviceService->getServiceByIds($ids);
        }

        return $this->serviceService->getRecentServices($posId, 1, 0, 0);
    }
}

