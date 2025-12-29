<?php

namespace App\Services\Setup\Customer;
use Illuminate\Http\Request;

interface ICustomerService{
    public function index();
    public function show($id);
    public function store(Request $request);
    public function edit($id);
    public function update(Request $request, $id);
    public function destroy($id);
}