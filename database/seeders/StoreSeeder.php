<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;

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
            'active' => true
        ]);
    }
}
