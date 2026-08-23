<?php

use App\Models\Role;
use App\Models\Staff;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Str;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$managerRole = Role::where('name', 'Manager')->first();
$cashierRole = Role::where('name', 'Cashier')->first();

if ($managerRole) {
    Staff::updateOrCreate(
        ['email' => 'manager@test.com'],
        [
            'id' => (string) Str::uuid(),
            'full_name' => 'Test Manager',
            'password_hash' => 'password123',
            'role_id' => $managerRole->id,
            'active' => true,
        ]
    );
}

if ($cashierRole) {
    Staff::updateOrCreate(
        ['email' => 'cashier@test.com'],
        [
            'id' => (string) Str::uuid(),
            'full_name' => 'Test Cashier',
            'password_hash' => 'password123',
            'role_id' => $cashierRole->id,
            'active' => true,
        ]
    );
}

echo "Berhasil membuat 2 test user.\n";
