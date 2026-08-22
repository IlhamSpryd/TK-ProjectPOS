<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$sql = "
    CREATE OR REPLACE FUNCTION is_staff_of_store(p_store_id UUID)
    RETURNS BOOLEAN AS $$
        SELECT EXISTS (
            SELECT 1 FROM staff_stores 
            WHERE staff_id = get_current_staff_id() 
            AND store_id = p_store_id
        );
    $$ LANGUAGE sql SECURITY DEFINER;
";

DB::connection('pgsql_admin')->unprepared($sql);
echo "Function is_staff_of_store updated successfully.\n";
