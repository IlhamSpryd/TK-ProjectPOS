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
        if (auth()->check()) {
            DB::statement(
                "SELECT set_config('app.staff_id', ?, false)",
                [(string) auth()->id()]
            );
        }

        $response = $next($request);

        // Reset to prevent leakage if connection is kept alive without pooler discard
        if (auth()->check()) {
            DB::statement("SELECT set_config('app.staff_id', '', false)");
        }

        return $response;
    }
}
