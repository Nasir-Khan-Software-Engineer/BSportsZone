<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=0; $i<15; $i++){
            $customer = new Customer();
            $customer->posid    = 1;
            $customer->name     = "Customer-".$i;
            $customer->gender   = "M";
            $customer->email    = "customer-".$i."@gmail.com";
            $customer->phone1   = "0163701796".$i;
            $customer->phone2   = "016370188".$i;
            $customer->address  = "Demo Address";
            $customer->note     = "this is note";
            $customer->created_by  = 1;
            $customer->save();
        }
    }
}
