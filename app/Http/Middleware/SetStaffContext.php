<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetStaffContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && DB::connection()->getDriverName() === 'pgsql') {
            // Note: The primary fix for connection pooling session leakage is handled 
            // by using Supabase Session Pooler (port 5432) instead of Transaction Pooler (6543).
            DB::statement(
                "SELECT set_config('app.staff_id', ?, false)",
                [(string) auth()->id()]
            );
        }

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Reset to prevent leakage if connection is kept alive without pooler discard.
        // This terminate() cleanup acts as a secondary safety net, though the main
        // fix is at the connection pooler level (Session Mode).
        if (auth()->check() && DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("SELECT set_config('app.staff_id', '', false)");
        }
    }
}
