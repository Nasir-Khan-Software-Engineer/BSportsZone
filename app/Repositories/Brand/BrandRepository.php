<?php

namespace App\Repositories\Brand;

use App\Models\Brand;

class BrandRepository implements IBrandRepository
{
    public function getBrands($POSID)
    {
        return Brand::with('creator')->where('POSID', $POSID)->get();
    }

    public function getBrand($POSID, $brandid)
    {
        return Brand::with('creator')->where('POSID', $POSID)->where('id', $brandid)->get();
    }

    public function createBrand($brand): void
    {
        $brand['created_by'] = auth()->user()->id;
        Brand::create($brand);
    }

    public function updateBrand($brand)
    {
        $brandInfo = Brand::with('creator')->where('POSID', $brand['POSID'])->where('id', $brand['id'])->find();

        $brandInfo->name = $brand['name'];
        $brandInfo->updated_by = auth()->user()->id;

        return $brand->save();
    }

    public function deleteBrand($POSID, $brandid)
    {
        return Brand::where('POSID', $POSID)->where('id', $brandid)->delete();
    }
}
