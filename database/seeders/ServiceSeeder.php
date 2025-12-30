<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=0; $i<10; $i++){
            $service = new Product();

            $service->POSID         = 1;
            $service->code          = "WIN-".$i;
            $service->name          = "Service-".$i;
            $service->unit_id       = 1;
            $service->brand_id          = 1;
            $service->price             = 200;
            $service->description       = "This is text Description";
            $service->created_by = 1;
            $service->type = "Service";

            $service->save();


            DB::table('category_product')->insert([
                'product_id' => $service->id,
                'category_id' => ($i + 1)
            ]);

            DB::table('category_product')->insert([
                'product_id' => $service->id,
                'category_id' => ($i + 2)
            ]);

        }
    }
}
