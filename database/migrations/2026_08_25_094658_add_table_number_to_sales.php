<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::connection('pgsql_admin')->unprepared(
            'ALTER TABLE sales ADD COLUMN IF NOT EXISTS table_number VARCHAR(50) NULL'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql_admin')->unprepared(
            'ALTER TABLE sales DROP COLUMN IF EXISTS table_number'
        );
    }
};
