<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=0; $i<10; $i++){
            $product = new Product();

            $product->posid         = 1;
            $product->code          = "WIN-".$i;
            $product->name          = "Product-".$i;
            $product->unit_id       = 1;
            $product->brand_id          = 1;
            $product->price             = 200;
            $product->description       = "This is text Description";
            $product->created_by = 1;

            $product->save();


            DB::table('category_product')->insert([
                'product_id' => $product->id,
                'category_id' => ($i + 1)
            ]);

            DB::table('category_product')->insert([
                'product_id' => $product->id,
                'category_id' => ($i + 2)
            ]);

        }
    }
}
