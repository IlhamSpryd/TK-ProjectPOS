<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Define Roles
        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['id' => (string) \Illuminate\Support\Str::uuid(), 'permissions' => ['*']]
        );

        $manager = Role::firstOrCreate(
            ['name' => 'Manager'],
            ['id' => (string) \Illuminate\Support\Str::uuid(), 'permissions' => [
                'pos_access', 'manage_catalog', 'manage_customers', 'manage_inventory', 'view_reports'
            ]]
        );

        $cashier = Role::firstOrCreate(
            ['name' => 'Cashier'],
            ['id' => (string) \Illuminate\Support\Str::uuid(), 'permissions' => [
                'pos_access', 'manage_customers'
            ]]
        );

        // Assign to user
        $staff = \App\Models\Staff::where('email', 'ilhamsepriyadi8@gmail.com')->first();
        if ($staff) {
            $staff->role_id = $superAdmin->id;
            $staff->save();
        }
    }
}
