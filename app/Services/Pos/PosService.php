<?php
namespace App\Services\Pos;

use App\Repositories\Pos\IPosRepository;
use App\Services\Service\IServiceService;
use App\Services\Product\IProductService;

class PosService implements IPosService{

    public function __construct(IPosRepository $iPosRepository,
                                IServiceService $iServiceService,
                                IProductService $iProductService){
        $this->posRepository = $iPosRepository;
        $this->serviceService = $iServiceService;
        $this->productService = $iProductService;
    }

    public function getPosPageItems($posId, $type = 'Product'){
        if($type == 'Product'){
            return $this->productService->getTopSellingProducts($posId);
        } else {
            return $this->serviceService->getTopSellingServices($posId);
        }
    }

}

