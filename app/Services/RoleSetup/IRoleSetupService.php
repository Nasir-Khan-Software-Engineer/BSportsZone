<?php

namespace App\Services\RoleSetup;
use Illuminate\Http\Request;

interface IRoleSetupService{
    public function store(Request $request);
    public function update(Request $request, $id);
}
