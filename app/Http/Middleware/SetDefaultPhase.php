<?php

namespace App\Http\Middleware;

use App\Models\Phase;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetDefaultPhase
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        URL::defaults(['working_phase' => Phase::getDefaultPhase()]);

        return $next($request);
    }
}
