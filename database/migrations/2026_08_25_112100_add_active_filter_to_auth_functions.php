<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Patch fungsi autentikasi SQL untuk memblokir login staff yang dinonaktifkan.
 *
 * Ketiga fungsi auth_get_staff_by_* sebelumnya hanya memfilter deleted_at IS NULL,
 * sehingga staff dengan active = false tetap bisa login. Migration ini menambahkan
 * filter AND active = true pada setiap fungsi.
 */
return new class extends Migration
{
    public function up(): void
    {
        // auth_get_staff_by_email: tambah filter active = true
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.auth_get_staff_by_email(p_email text)
            RETURNS SETOF public.staff
            LANGUAGE sql STABLE SECURITY DEFINER
            SET search_path TO 'public', 'pg_temp'
            AS \$\$
                SELECT * FROM public.staff
                WHERE email = p_email AND deleted_at IS NULL AND active = true
                LIMIT 1;
            \$\$;
        ");

        // auth_get_staff_by_id: tambah filter active = true
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.auth_get_staff_by_id(p_id uuid)
            RETURNS SETOF public.staff
            LANGUAGE sql STABLE SECURITY DEFINER
            SET search_path TO 'public', 'pg_temp'
            AS \$\$
                SELECT * FROM public.staff
                WHERE id = p_id AND deleted_at IS NULL AND active = true
                LIMIT 1;
            \$\$;
        ");

        // auth_get_staff_by_remember_token: tambah filter active = true
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.auth_get_staff_by_remember_token(p_id uuid, p_token text)
            RETURNS SETOF public.staff
            LANGUAGE sql STABLE SECURITY DEFINER
            SET search_path TO 'public', 'pg_temp'
            AS \$\$
                SELECT * FROM public.staff
                WHERE id = p_id AND remember_token = p_token AND deleted_at IS NULL AND active = true
                LIMIT 1;
            \$\$;
        ");
    }

    public function down(): void
    {
        // Rollback: kembalikan ke versi tanpa filter active
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.auth_get_staff_by_email(p_email text)
            RETURNS SETOF public.staff
            LANGUAGE sql STABLE SECURITY DEFINER
            SET search_path TO 'public', 'pg_temp'
            AS \$\$
                SELECT * FROM public.staff WHERE email = p_email AND deleted_at IS NULL LIMIT 1;
            \$\$;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.auth_get_staff_by_id(p_id uuid)
            RETURNS SETOF public.staff
            LANGUAGE sql STABLE SECURITY DEFINER
            SET search_path TO 'public', 'pg_temp'
            AS \$\$
                SELECT * FROM public.staff WHERE id = p_id AND deleted_at IS NULL LIMIT 1;
            \$\$;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.auth_get_staff_by_remember_token(p_id uuid, p_token text)
            RETURNS SETOF public.staff
            LANGUAGE sql STABLE SECURITY DEFINER
            SET search_path TO 'public', 'pg_temp'
            AS \$\$
                SELECT * FROM public.staff
                WHERE id = p_id AND remember_token = p_token AND deleted_at IS NULL
                LIMIT 1;
            \$\$;
        ");
    }
};
