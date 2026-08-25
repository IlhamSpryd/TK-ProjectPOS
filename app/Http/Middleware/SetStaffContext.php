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
        // Early return for static assets, Livewire JS, debugbar, and non-GET/POST/PUT/DELETE
        if ($request->isMethod('HEAD') || $request->isMethod('OPTIONS') ||
            $request->is('livewire/livewire.js', 'livewire/livewire.js.map', '_debugbar/*', 'build/*', 'assets/*')) {
            return $next($request);
        }

        if (auth()->check() && DB::connection()->getDriverName() === 'pgsql') {
            // Note: The primary fix for connection pooling session leakage is handled
            // by using Supabase Session Pooler (port 5432) instead of Transaction Pooler (6543).
            $staff = auth()->user();
            DB::select('SELECT set_config(?, ?, false)', ['app.staff_id', (string) $staff->id]);
            DB::select('SELECT set_config(?, ?, false)', ['app.current_tenant_id', (string) $staff->tenant_id]);
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
            DB::select('SELECT set_config(?, ?, true)', ['app.staff_id', '']);
            DB::select('SELECT set_config(?, ?, true)', ['app.current_tenant_id', '']);
        }
    }
}
