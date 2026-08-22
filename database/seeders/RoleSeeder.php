<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Define Roles
        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['id' => (string) Str::uuid(), 'permissions' => ['*']]
        );

        $manager = Role::firstOrCreate(
            ['name' => 'Manager'],
            ['id' => (string) Str::uuid(), 'permissions' => [
                'pos_access', 'manage_catalog', 'manage_customers', 'manage_inventory', 'view_reports',
            ]]
        );

        $cashier = Role::firstOrCreate(
            ['name' => 'Cashier'],
            ['id' => (string) Str::uuid(), 'permissions' => [
                'pos_access', 'manage_customers',
            ]]
        );

        // Assign to user
        $staff = Staff::where('email', 'ilhamsepriyadi8@gmail.com')->first();
        if ($staff) {
            $staff->role_id = $superAdmin->id;
            $staff->save();
        }
    }
}
