<?php

namespace App\Services\ShopSetup;
use Illuminate\Http\Request;

interface IShopSetupService{
    public function store(Request $request);
    public function update(Request $request, $id);
}
