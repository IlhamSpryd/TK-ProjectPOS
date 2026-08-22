<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        Store::create([
            'name' => 'Toko Pusat',
            'business_type' => 'retail',
            'is_pkp' => true,
            'currency' => 'IDR',
            'timezone' => 'Asia/Jakarta',
            'active' => true,
        ]);
    }
}
