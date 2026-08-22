<?php

use Illuminate\Support\Facades\DB;

// Gunakan koneksi biasa untuk pengujian RLS (Connection pooler port 5432)
$conn = DB::connection('pgsql');

$storeId = DB::table('stores')->value('id');
if (!$storeId) {
    $storeId = \Illuminate\Support\Str::uuid();
    DB::connection('pgsql_admin')->table('stores')->insert([
        'id' => $storeId,
        'name' => 'Verify Store'
    ]);
}

$staffId = DB::table('staff')->value('id');
if (!$staffId) {
    $staffId = \Illuminate\Support\Str::uuid();
    $email = 'verify'.rand().'@test.com';
    
    DB::connection('pgsql_admin')->table('staff')->insert([
        'id' => $staffId,
        'name' => 'Verify Staff',
        'email' => $email,
        'password' => 'secret',
        'role' => 'cashier',
        'status' => 'active'
    ]);
}

$staff = DB::table('staff_stores')->where('store_id', $storeId)->first();
if (!$staff) {
    DB::connection('pgsql_admin')->table('staff_stores')->insert([
        'staff_id' => $staffId,
        'store_id' => $storeId,
        'is_primary' => true
    ]);
} else {
    $staffId = $staff->staff_id;
}

echo "Using Real Store ID: $storeId\n";
echo "Using Real Staff ID: $staffId\n";

$stores = DB::table('staff_stores')->where('staff_id', $staffId)->get();
echo "STAFF STORES: \n";
foreach($stores as $s) {
    echo " - Store: " . $s->store_id . "\n";
}

echo "--- VERIFY RLS ---\n";

// Skenario 1: Insert tanpa setting staff_id -> HARUS GAGAL
try {
    DB::table('sales')->insert([
        'id' => \Illuminate\Support\Str::uuid(),
        'store_id' => $storeId,
        'staff_id' => $staffId,
        'sale_date' => now(),
        'sale_number' => 'VERIFY-1',
        'subtotal' => 1000,
        'grand_total' => 1000,
        'payment_status' => 'unpaid',
        'status' => 'open'
    ]);
    echo "ERROR: Insert BERHASIL tanpa staff context (RLS BOCOR!)\n";
} catch (\Exception $e) {
    echo "OK: Insert gagal tanpa staff context. (" . $e->getMessage() . ")\n";
}

// Skenario 2: Insert dengan setting staff_id -> HARUS BERHASIL
try {
    DB::transaction(function() use ($storeId, $staffId) {
        DB::statement("SET LOCAL app.staff_id = '{$staffId}'");
        
        $currentStaff = DB::selectOne("SELECT get_current_staff_id() as id");
        echo "CURRENT CONTEXT STAFF ID: " . $currentStaff->id . "\n";
        
        $isStaff = DB::selectOne("SELECT is_staff_of_store(?) as is_staff", [$storeId]);
        echo "IS STAFF OF STORE: " . ($isStaff->is_staff ? 'true' : 'false') . "\n";

        DB::table('sales')->insert([
            'id' => \Illuminate\Support\Str::uuid(),
            'store_id' => $storeId,
            'staff_id' => $staffId,
            'sale_date' => now(),
            'sale_number' => 'VERIFY-' . rand(),
            'subtotal' => 1000,
            'grand_total' => 1000,
            'payment_status' => 'unpaid',
            'status' => 'open'
        ]);
    });
    echo "OK: Insert BERHASIL dengan staff context.\n";
} catch (\Exception $e) {
    echo "ERROR: Insert gagal meskipun ada staff context. (" . $e->getMessage() . ")\n";
}

// Skenario 3: Insert untuk toko yang staff tidak punya akses -> HARUS GAGAL
try {
    DB::transaction(function() use ($staffId) {
        DB::statement("SET LOCAL app.staff_id = '{$staffId}'");
        DB::table('sales')->insert([
            'id' => \Illuminate\Support\Str::uuid(),
            'store_id' => \Illuminate\Support\Str::uuid(), // Toko ngarang
            'staff_id' => $staffId,
            'sale_date' => now(),
            'sale_number' => 'VERIFY-INVALID',
            'subtotal' => 1000,
            'grand_total' => 1000,
            'payment_status' => 'unpaid',
            'status' => 'open'
        ]);
    });
    echo "ERROR: Insert BERHASIL untuk toko yang salah (RLS BOCOR!)\n";
} catch (\Exception $e) {
    echo "OK: Insert gagal untuk toko yang salah. (" . $e->getMessage() . ")\n";
}
