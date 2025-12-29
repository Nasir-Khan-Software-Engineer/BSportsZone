<?php

namespace App\Repositories\ShopSetup;
use App\Models\Shop;
use DB;

class shopSetupRepository implements IShopSetupRepository{
    public function __construct(){
        // inject dependencies
    }

    public function store($shopInfo,$metaData){

        $shop = new Shop();
        $shop->name             = $shopInfo['name'];
        $shop->email            = $shopInfo['email'];
        $shop->primaryPhone     = $shopInfo['primaryPhone'];
        $shop->secondaryPhone   = $shopInfo['secondaryPhone'];
        $shop->division         = $shopInfo['division'];
        $shop->district         = $shopInfo['district'];
        $shop->thana            = $shopInfo['thana'];
        $shop->address          = $shopInfo['address'];
        $shop->about            = $shopInfo['about'];
        $shop->POSID            = $metaData->POSID;
        $shop->created_by       = $metaData->createdBy;
        $shop->save();
        
        return $shop;
    }

    public function update($shopInfo,$metaData){

        $shop = Shop::where('posid', $metaData->POSID)
            ->where('id', $metaData->shopID)
            ->first();


        $shop->name             = $shopInfo['name'];
        $shop->email            = $shopInfo['email'];
        $shop->primaryPhone     = $shopInfo['primaryPhone'];
        $shop->secondaryPhone   = $shopInfo['secondaryPhone'];
        $shop->division         = $shopInfo['division'];
        $shop->district         = $shopInfo['district'];
        $shop->thana            = $shopInfo['thana'];
        $shop->address          = $shopInfo['address'];
        $shop->about            = $shopInfo['about'];
        $shop->updated_by       = $metaData->updatedBy;
        $shop->save();
        
        return $shop;
    }
}