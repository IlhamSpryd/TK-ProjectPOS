-- =============================================================================
-- PATCH: Tambah filter active = true pada fungsi autentikasi staff
-- =============================================================================
-- Jalankan SQL ini langsung di Supabase SQL Editor jika migration Laravel
-- tidak dapat dijalankan melalui pooler.
-- =============================================================================

-- 1. auth_get_staff_by_email
CREATE OR REPLACE FUNCTION public.auth_get_staff_by_email(p_email text)
RETURNS SETOF public.staff
LANGUAGE sql STABLE SECURITY DEFINER
SET search_path TO 'public', 'pg_temp'
AS $$
    SELECT * FROM public.staff
    WHERE email = p_email AND deleted_at IS NULL AND active = true
    LIMIT 1;
$$;

-- 2. auth_get_staff_by_id
CREATE OR REPLACE FUNCTION public.auth_get_staff_by_id(p_id uuid)
RETURNS SETOF public.staff
LANGUAGE sql STABLE SECURITY DEFINER
SET search_path TO 'public', 'pg_temp'
AS $$
    SELECT * FROM public.staff
    WHERE id = p_id AND deleted_at IS NULL AND active = true
    LIMIT 1;
$$;

-- 3. auth_get_staff_by_remember_token
CREATE OR REPLACE FUNCTION public.auth_get_staff_by_remember_token(p_id uuid, p_token text)
RETURNS SETOF public.staff
LANGUAGE sql STABLE SECURITY DEFINER
SET search_path TO 'public', 'pg_temp'
AS $$
    SELECT * FROM public.staff
    WHERE id = p_id AND remember_token = p_token AND deleted_at IS NULL AND active = true
    LIMIT 1;
$$;
