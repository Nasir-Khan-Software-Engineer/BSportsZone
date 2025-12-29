<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=1; $i<=5; $i++){
            $supplier = new Supplier();
            $supplier->posid = 1;
            $supplier->name = "Supplier-".$i;
            $supplier->email = "supplier-".$i."@gmail.com";
            $supplier->phone = "0163701792".$i;
            $supplier->address = "Dhaka, Bangladesh, Dhaka";
            $supplier->note = "This is demo note";
            $supplier->created_by = 1;
            $supplier->save();
        }
    }
}
