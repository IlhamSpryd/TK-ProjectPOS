<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\DB;

class SetStaffContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($staff = $request->user()) {
            DB::statement("SET app.staff_id = '" . $staff->id . "'");
        } else {
            DB::statement("SET app.staff_id = ''");
        }
        
        return $next($request);
    }
}
