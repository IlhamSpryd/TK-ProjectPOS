<?php
use Illuminate\Support\Facades\DB;

$storeId = DB::table('stores')->value('id');
if (!$storeId) {
    $storeId = '9cbeb156-10a6-4331-a1bf-35e226b7e6dc';
    try {
        DB::table('stores')->insert([
            'id' => $storeId,
            'name' => 'Benchmark Store',
            'address' => 'Dummy',
            'phone' => '123'
        ]);
    } catch (\Exception $e) {}
}

echo "Generating 1 million dummy sales data for Benchmark...\n";
$start_time = microtime(true);

$sqlSales = "
INSERT INTO sales (id, store_id, staff_id, customer_id, register_id, shift_id, sale_date, sale_number, subtotal, discount_total, tax_total, service_charge_total, grand_total, payment_status, status, created_at, updated_at)
SELECT 
    gen_random_uuid(),
    '{$storeId}',
    '00000000-0000-0000-0000-000000000000',
    NULL,
    NULL,
    NULL,
    NOW() - (random() * (interval '365 days')),
    'SALE-BENCH-' || s,
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
DB::unprepared("SET statement_timeout = 0;");
DB::unprepared($sqlSales);
$end_time = microtime(true);
echo "1 Million Sales inserted in " . ($end_time - $start_time) . " seconds.\n";

echo "Running EXPLAIN ANALYZE on Heavy Query (Before Index):\n";
$query = "EXPLAIN ANALYZE SELECT * FROM sales s WHERE s.store_id = ? ORDER BY s.sale_date DESC LIMIT 50";
$explain = DB::select($query, [$storeId]);
foreach ($explain as $row) {
    echo $row->{'QUERY PLAN'} . "\n";
}
