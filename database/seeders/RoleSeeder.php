<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'admin', 'permissions' => ['*']]);
        Role::create(['name' => 'manager', 'permissions' => ['manage_users', 'view_reports']]);
        Role::create(['name' => 'cashier', 'permissions' => ['create_sales']]);
    }
}
