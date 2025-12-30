<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=1; $i<=15; $i++){
            $brand = new Brand;
            $brand->POSID = 1;
            $brand->name = "Brand-".$i;
            $brand->logo = "demo";
            $brand->description = "demo description";
            $brand->note = "note";
            $brand->created_by = 1;
            $brand->save();
        }
    }
}
