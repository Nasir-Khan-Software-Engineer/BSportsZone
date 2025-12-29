<?php
namespace App\Repositories\ShopSetup;

interface IshopSetupRepository{
    public function store($shopInfo,$metaData);
    public function update($shopInfo,$metaData);
}
