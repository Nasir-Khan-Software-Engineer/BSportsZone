<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shop;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=0;$i<10;$i++){
            $shop = new Shop();
            $shop->POSID = 1;
            $shop->name = "Shop-".$i;
            $shop->email = "shop-".$i."@gmail.com";
            $shop->primaryPhone = "0163701792".$i;
            $shop->address = "Dhaka, Bangladesh, Dhaka";
            $shop->district = "Dhaka";
            $shop->division = "Dhaka";
            $shop->thana = "Dhaka";
            $shop->about = "Dhaka";
            $shop->save();
        }
    }
}
