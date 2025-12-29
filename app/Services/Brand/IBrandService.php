<?php

namespace App\Services\Brand;

use App\Models\Brand;

interface IBrandService
{
    public function getBrands($posid);
    public function getBrand($posid, $brandid);
    public function createBrand($brand);
    public function updateBrand($brand);
    public function deleteBrand($posid, $brandid);
}
