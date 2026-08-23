<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\Store;
use Illuminate\Database\Seeder;

class StaffStoreSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Staff::where('email', 'admin@example.com')->first();
        $store = Store::where('name', 'Toko Pusat')->first();

        if ($admin && $store) {
            $admin->stores()->attach($store->id, ['is_primary' => true]);
        }
    }
}
