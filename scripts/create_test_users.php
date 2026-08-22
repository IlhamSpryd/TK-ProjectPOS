<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$managerRole = \App\Models\Role::where('name', 'Manager')->first();
$cashierRole = \App\Models\Role::where('name', 'Cashier')->first();

if ($managerRole) {
    \App\Models\Staff::updateOrCreate(
        ['email' => 'manager@test.com'],
        [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'full_name' => 'Test Manager',
            'password_hash' => 'password123',
            'role_id' => $managerRole->id,
            'active' => true,
        ]
    );
}

if ($cashierRole) {
    \App\Models\Staff::updateOrCreate(
        ['email' => 'cashier@test.com'],
        [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'full_name' => 'Test Cashier',
            'password_hash' => 'password123',
            'role_id' => $cashierRole->id,
            'active' => true,
        ]
    );
}

echo "Berhasil membuat 2 test user.\n";
