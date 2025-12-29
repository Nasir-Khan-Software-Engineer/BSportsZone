<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\AccessRight;

class AssignAllAdminAccessRightsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all access rights
        $allAccessRights = AccessRight::all();

        $adminRoles = Role::whereRaw('LOWER(name) = ?', ['admin'])->get();

        foreach ($adminRoles as $role) {
            $role->accessRights()->syncWithoutDetaching($allAccessRights);
            $this->command->info("✅ All access rights assigned to Admin role for POSID {$role->POSID}");
        }
    }
}
