<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Accountinfo;
class AccountinfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $account = new Accountinfo();
        $account->POSID = 1;
        $account->companyName = "Demo Company";
        $account->primaryEmail = "demo@gmail.com";
        $account->primaryPhone = "1111111111";
        $account->division = "Dhaka";
        $account->district = "Dhaka";
        $account->area = "Dhaka";
        $account->address = "Dhaka, Dhaka, 1209";;
        $account->save();

        $account = new Accountinfo();
        $account->POSID = 2;
        $account->companyName = "Demo Company";
        $account->primaryEmail = "demo@gmail.com";
        $account->primaryPhone = "1111111111";
        $account->division = "Dhaka";
        $account->district = "Dhaka";
        $account->area = "Dhaka";
        $account->address = "Dhaka, Dhaka, 1209";;
        $account->save();

    }
}
