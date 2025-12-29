<?php

namespace App\Services\Brand;

use App\Models\Brand;
use App\Repositories\Brand\IBrandRepository;

class BrandService implements IBrandService
{
    public function __construct(IBrandRepository $iBrandRepository)
    {
        $this->brandRepository = $iBrandRepository;
    }

    public function getBrands($posid)
    {
        return $this->brandRepository->getBrands($posid);
    }

    public function getBrand($posid, $brandid)
    {
        return $this->brandRepository->getBrand($posid, $brandid);
    }

    public function createBrand($brand): void
    {
        $this->brandRepository->createBrand($brand);
    }

    public function updateBrand($brand)
    {
        return $this->brandRepository->updateBrand($brand);
    }

    public function deleteBrand($posid, $brandid)
    {
        return $this->brandRepository->deleteBrand($posid, $brandid);
    }
}
