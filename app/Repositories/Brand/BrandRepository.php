<?php

namespace App\Repositories\Brand;

use App\Models\Brand;

class BrandRepository implements IBrandRepository
{
    public function getBrands($posid)
    {
        return Brand::with('creator')->where('posid', $posid)->get();
    }

    public function getBrand($posid, $brandid)
    {
        return Brand::with('creator')->where('posid', $posid)->where('id', $brandid)->get();
    }

    public function createBrand($brand): void
    {
        $brand['created_by'] = auth()->user()->id;
        Brand::create($brand);
    }

    public function updateBrand($brand)
    {
        $brandInfo = Brand::with('creator')->where('posid', $brand['posid'])->where('id', $brand['id'])->find();

        $brandInfo->name = $brand['name'];
        $brandInfo->updated_by = auth()->user()->id;

        return $brand->save();
    }

    public function deleteBrand($posid, $brandid)
    {
        return Brand::where('posid', $posid)->where('id', $brandid)->delete();
    }
}
