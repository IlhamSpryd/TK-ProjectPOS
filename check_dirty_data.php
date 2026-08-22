<?php
// Check for dirty data in sales and payments
$dirtySales = DB::select("SELECT id, status, payment_status FROM sales WHERE status NOT IN ('draft','open','completed','void','refunded') OR payment_status NOT IN ('unpaid','partial','paid','refunded') LIMIT 5");
$dirtyPayments = DB::select("SELECT id, amount FROM payments WHERE amount < 0 LIMIT 5");
$dirtySaleItems = DB::select("SELECT id, quantity FROM sale_items WHERE quantity <= 0 LIMIT 5");

if (count($dirtySales) > 0) { echo "DIRTY SALES FOUND\n"; print_r($dirtySales); } else { echo "SALES CLEAN\n"; }
if (count($dirtyPayments) > 0) { echo "DIRTY PAYMENTS FOUND\n"; print_r($dirtyPayments); } else { echo "PAYMENTS CLEAN\n"; }
if (count($dirtySaleItems) > 0) { echo "DIRTY SALE ITEMS FOUND\n"; print_r($dirtySaleItems); } else { echo "SALE ITEMS CLEAN\n"; }

// Baseline Explain Analyze for a typical report query
$storeId = DB::table('stores')->value('id');
if ($storeId) {
    echo "\nBASELINE EXPLAIN ANALYZE:\n";
    $query = "EXPLAIN ANALYZE SELECT * FROM sales s JOIN sale_items si ON s.id = si.sale_id WHERE s.store_id = ? ORDER BY s.sale_date DESC LIMIT 50";
    $explain = DB::select($query, [$storeId]);
    foreach ($explain as $row) {
        echo $row->{'QUERY PLAN'} . "\n";
    }
}
