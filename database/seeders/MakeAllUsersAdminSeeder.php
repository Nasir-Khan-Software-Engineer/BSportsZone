<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
class MakeAllUsersAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all Admin roles, keyed by POSID
        $adminRoles = Role::whereRaw('LOWER(name) = ?', ['admin'])
                          ->get()
                          ->keyBy('POSID');
        //dd($adminRoles);
        // Assign Admin role to all users based on their POSID
        $users = User::all();
        foreach ($users as $user) {
            if (isset($adminRoles[$user->POSID])) {
                $user->role_id = $adminRoles[$user->POSID]->id;
                $user->save();
                $this->command->info("✅ User {$user->id} assigned Admin role for POSID {$user->POSID}");
            } else {
                $this->command->warn("⚠️ No Admin role found for POSID {$user->POSID}, user {$user->id} skipped.");
            }
        }
    }
}
