<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $superAdmin = Role::create(['name' => 'SuperAdmin']);
        $adminMonitor = Role::create(['name' => 'AdminMonitor']);
        $schoolAdmin = Role::create(['name' => 'SchoolAdmin']);

        Permission::create(['name' => 'view_reports']);
        Permission::create(['name' => 'edit_transactions']);
        Permission::create(['name' => 'manage_schools']);

        $superAdmin->givePermissionTo(['view_reports', 'manage_schools']);
        $adminMonitor->givePermissionTo(['view_reports', 'manage_schools']);
        $schoolAdmin->givePermissionTo(['view_reports', 'edit_transactions']);
    }
}
