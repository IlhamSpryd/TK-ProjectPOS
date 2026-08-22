<?php
use Illuminate\Support\Facades\DB;

$storeId = DB::table('stores')->value('id');
if (!$storeId) {
    echo "NO STORE FOUND. Cannot generate dummy data.\n";
    exit;
}
$userId = DB::table('users')->value('id');
if (!$userId) {
    // Insert a dummy user if none exists
    $userId = '00000000-0000-0000-0000-000000000000';
    DB::table('users')->insert([
        'id' => $userId,
        'name' => 'Dummy',
        'email' => 'dummy@example.com',
        'password' => bcrypt('password')
    ]);
}

echo "Store ID: $storeId\n";
echo "User ID: $userId\n";

$start_time = microtime(true);
echo "Generating 100k sales...\n";

// Use PostgreSQL generate_series for extremely fast bulk insert
$sqlSales = "
INSERT INTO sales (id, store_id, staff_id, customer_id, register_id, shift_id, sale_number, sale_date, subtotal, discount_total, tax_total, service_charge_total, grand_total, payment_status, status, created_at, updated_at)
SELECT 
    gen_random_uuid(),
    '{$storeId}',
    '{$userId}',
    NULL,
    NULL,
    NULL,
    NOW() - (random() * (interval '365 days')),
    'SALE-' || s,
    100000,
    0,
    10000,
    0,
    110000,
    'paid',
    'completed',
    NOW(),
    NOW()
FROM generate_series(1, 100000) AS s;
";
// Note: Inserting 1,000,000 at once might hit memory/WAL limits on local/Supabase free tier. Let's do 100,000.
DB::unprepared("SET statement_timeout = 0;");
DB::unprepared($sqlSales);

$end_time = microtime(true);
echo "Sales inserted in " . ($end_time - $start_time) . " seconds.\n";

echo "Generating 1.5 million sale_items...\n";
$variantId = DB::table('product_variants')->value('id');

$sqlSaleItems = "
INSERT INTO sale_items (id, sale_id, variant_id, quantity, unit_price, subtotal, created_at, updated_at)
SELECT 
    gen_random_uuid(),
    id,
    '{$variantId}',
    1,
    100000,
    100000,
    NOW(),
    NOW()
FROM sales
WHERE NOT EXISTS (SELECT 1 FROM sale_items si WHERE si.sale_id = sales.id)
LIMIT 100000;
";
$start_time = microtime(true);
DB::unprepared($sqlSaleItems);
$end_time = microtime(true);
echo "Sale items inserted in " . ($end_time - $start_time) . " seconds.\n";
