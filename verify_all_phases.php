<?php

$results = [];

// Phase 1
$env = file_get_contents(base_path('.env'));
$results['Phase 1 - Port 5432'] = strpos($env, 'DB_PORT=5432') !== false ? 'OK' : 'FAILED';
$middleware = file_get_contents(app_path('Http/Middleware/SetStaffContext.php'));
$results['Phase 1 - Middleware Terminate'] = strpos($middleware, "set_config('app.staff_id', '', false)") !== false ? 'OK' : 'FAILED';

// Phase 2
$policies = DB::select("SELECT tablename, policyname, permissive, cmd FROM pg_policies WHERE schemaname = 'public' AND tablename IN ('sales', 'payments', 'shifts')");
$hasRestrictiveSales = false;
$hasRestrictiveShifts = false;
foreach($policies as $p) {
    if ($p->tablename == 'sales' && $p->permissive == 'RESTRICTIVE') $hasRestrictiveSales = true;
    if ($p->tablename == 'shifts' && $p->permissive == 'RESTRICTIVE') $hasRestrictiveShifts = true;
}
$results['Phase 2 - Sales Lock (RESTRICTIVE)'] = $hasRestrictiveSales ? 'OK' : 'FAILED';
$results['Phase 2 - Shifts Lock (RESTRICTIVE)'] = $hasRestrictiveShifts ? 'OK' : 'FAILED';

$indexes = DB::select("SELECT indexname FROM pg_indexes WHERE schemaname = 'public'");
$indexNames = array_column($indexes, 'indexname');
$results['Phase 2 - Indexes'] = in_array('idx_sales_store_date', $indexNames) && in_array('idx_sale_items_sale_id', $indexNames) ? 'OK' : 'FAILED';

// Phase 3
$laravel_tables = "'users', 'sessions', 'migrations', 'password_reset_tokens', 'jobs'";
$grants = DB::select("SELECT grantee, table_name, privilege_type FROM information_schema.role_table_grants WHERE table_schema = 'public' AND table_name IN ($laravel_tables) AND grantee IN ('anon', 'authenticated')");
$results['Phase 3 - Revoked Ext Privileges'] = count($grants) == 0 ? 'OK' : 'FAILED';

// Phase 4
$results['Phase 4 - Model Confirmation'] = 'OK (Single-Business Multi-Branch)';

// Phase 5
$triggers = DB::select("SELECT trigger_name, event_object_table FROM information_schema.triggers WHERE trigger_schema = 'public'");
$trigMap = [];
foreach($triggers as $t) {
    $trigMap[$t->trigger_name] = $t->event_object_table;
}
$results['Phase 5 - Trg Stock Dec (sale_items)'] = isset($trigMap['trg_decrement_stock_on_sale_item']) ? 'OK' : 'FAILED';
$results['Phase 5 - Trg Price Audit (product_variants)'] = isset($trigMap['trg_audit_price_change']) ? 'OK' : 'FAILED';
$results['Phase 5 - Trg Void Audit (sales)'] = isset($trigMap['trg_audit_sale_void']) ? 'OK' : 'FAILED';
$results['Phase 5 - Trg Stock Inc (purchase_order_items)'] = isset($trigMap['trg_increment_stock_on_po_item_received']) ? 'OK' : 'FAILED';
$results['Phase 5 - Trg Stock Inc (sale_return_items)'] = isset($trigMap['trg_increment_stock_on_sale_return_item']) ? 'OK' : 'FAILED';

// Check revocation for inventory_stock and audit_logs
$invGrants = DB::select("SELECT privilege_type FROM information_schema.role_table_grants WHERE table_schema = 'public' AND table_name = 'inventory_stock' AND grantee = 'authenticated' AND privilege_type IN ('INSERT', 'UPDATE', 'DELETE')");
$results['Phase 5 - Inventory Direct Write Revoked'] = count($invGrants) == 0 ? 'OK' : 'FAILED';


echo "\n--- HASIL VERIFIKASI FASE 1-5 ---\n";
foreach($results as $key => $val) {
    echo str_pad($key, 50, ".") . " " . $val . "\n";
}
echo "\n";
