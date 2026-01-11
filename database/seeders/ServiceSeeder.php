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
            
            // Random discount for services (50% chance)
            if(rand(0, 1) === 1){
                $discountType = rand(0, 1) === 1 ? 'percentage' : 'fixed';
                if($discountType === 'percentage'){
                    $service->discount_type = 'percentage';
                    $service->discount_value = rand(5, 30); // 5% to 30%
                } else {
                    $service->discount_type = 'fixed';
                    $service->discount_value = rand(10, 50); // 10tk to 50tk
                }
            } else {
                $service->discount_type = null;
                $service->discount_value = null;
            }

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
