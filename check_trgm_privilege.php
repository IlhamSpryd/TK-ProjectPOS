<?php
try {
    DB::unprepared("CREATE EXTENSION IF NOT EXISTS pg_trgm;");
    echo "SUCCESS: tkpos_app can create extension";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
