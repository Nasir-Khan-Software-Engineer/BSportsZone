<?php

namespace App\Services\Brand;

use App\Models\Brand;

interface IBrandService
{
    public function getBrands($POSID);
    public function getBrand($POSID, $brandid);
    public function createBrand($brand);
    public function updateBrand($brand);
    public function deleteBrand($POSID, $brandid);
}
