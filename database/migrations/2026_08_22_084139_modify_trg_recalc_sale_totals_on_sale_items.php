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
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::connection('pgsql_admin')->unprepared('
                DROP TRIGGER IF EXISTS trg_recalc_sale_totals ON public.sale_items;
                CREATE TRIGGER trg_recalc_sale_totals 
                AFTER DELETE OR UPDATE ON public.sale_items 
                FOR EACH ROW 
                EXECUTE FUNCTION public.recalc_sale_totals();
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::connection('pgsql_admin')->unprepared('
                DROP TRIGGER IF EXISTS trg_recalc_sale_totals ON public.sale_items;
                CREATE TRIGGER trg_recalc_sale_totals 
                AFTER INSERT OR DELETE OR UPDATE ON public.sale_items 
                FOR EACH ROW 
                EXECUTE FUNCTION public.recalc_sale_totals();
            ');
        }
    }
};
