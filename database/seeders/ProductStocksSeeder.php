<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductStock;

class ProductStocksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $i = 0;

        while($i<10){
            $ps =  new ProductStock();
            $ps->product_id = $i+1;
            $ps->change_type = 'IN';
            $ps->quantity = 60;
            $ps->price = 120;
            $ps->discount = 0;
            $ps->tax = 0;
            $ps->reference_type = 'PURCHASE';
            $ps->reference_id = $i + 1;
            $ps->created_by = 1;

            $ps->save();

            $i++;
        }
    }
}
