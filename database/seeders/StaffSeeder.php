<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        Staff::create([
            'role_id' => $adminRole->id,
            'full_name' => 'Admin POS',
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('password'),
            'active' => true,
        ]);
    }
}
