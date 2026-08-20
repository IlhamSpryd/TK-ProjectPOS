-- Role untuk aplikasi Laravel (non-superuser)
create role laravel_app login password 'laravel_app_password';
grant usage on schema public to laravel_app;
grant select, insert, update, delete on all tables in schema public to laravel_app;
grant usage, select on all sequences in schema public to laravel_app;
alter default privileges in schema public grant select, insert, update, delete on tables to laravel_app;
alter default privileges in schema public grant usage, select on sequences to laravel_app;
