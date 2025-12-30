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

    public function getBrands($POSID)
    {
        return $this->brandRepository->getBrands($POSID);
    }

    public function getBrand($POSID, $brandid)
    {
        return $this->brandRepository->getBrand($POSID, $brandid);
    }

    public function createBrand($brand): void
    {
        $this->brandRepository->createBrand($brand);
    }

    public function updateBrand($brand)
    {
        return $this->brandRepository->updateBrand($brand);
    }

    public function deleteBrand($POSID, $brandid)
    {
        return $this->brandRepository->deleteBrand($POSID, $brandid);
    }
}
