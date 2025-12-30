<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //User::truncate();

        $user = new User();
        $user->POSID = 1;
        $user->name = "Aura17";
        $user->email = "Aura17@gmail.com";
        $user->password  = Hash::make("12345678");
        $user->role_id = 1;
        $user->save();

        $user = new User();
        $user->POSID = 1;
        $user->name = "Admin";
        $user->email = "admin@gmail.com";
        $user->password  = Hash::make("12345678");
        $user->role_id = 2;
        $user->save();

        $user = new User();
        $user->POSID = 2;
        $user->name = "BackupAccount";
        $user->email = "Aura17Ba@gmail.com";
        $user->password  = Hash::make("12345678");
        $user->role_id = 1;
        $user->save();
    }
}
