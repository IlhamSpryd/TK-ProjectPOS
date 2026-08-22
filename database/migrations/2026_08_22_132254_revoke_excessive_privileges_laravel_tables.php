<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'users',
            'sessions',
            'password_reset_tokens',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'passkeys',
            'migrations'
        ];

        foreach ($tables as $table) {
            // Check if table exists before revoking
            $exists = DB::connection('pgsql_admin')->selectOne(
                "SELECT EXISTS (
                    SELECT FROM information_schema.tables 
                    WHERE table_schema = 'public' 
                    AND table_name = ?
                )",
                [$table]
            )->exists;

            if ($exists) {
                DB::connection('pgsql_admin')->statement("REVOKE ALL ON {$table} FROM anon, authenticated");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-granting is generally not safe/necessary for these tables, but we can restore default Supabase grants if absolutely needed.
        // However, for internal Laravel tables, they shouldn't be exposed anyway.
    }
};
