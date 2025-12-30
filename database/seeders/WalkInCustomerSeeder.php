<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class WalkInCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'id' => 0,
            'POSID' => 0,
            'name' => 'Walk-In',
            'gender' => '',
            'email'=> '',
            'phone1' => '',
            'address' => '',
            'isActive' => true,
        ]);
    }
}
