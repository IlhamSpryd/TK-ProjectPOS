<?php
try {
    config(['database.connections.pgsql.username' => 'postgres']);
    DB::reconnect();
    DB::unprepared("SELECT 1");
    echo "SUCCESS WITH POSTGRES ROLE";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
