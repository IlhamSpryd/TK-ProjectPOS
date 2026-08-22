<?php
try {
    DB::unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_test_staff_role ON staff(role_id)");
    echo "SUCCESS INDEX";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
