<?php


namespace App\Http\Middleware;

use App\Helpers\Utilities;
use Closure;
use Hexbatch\Things\Interfaces\IThingOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetThingOwner
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $namespace_to_use = Utilities::getThisUserDefaultNamespace();
        if (!$namespace_to_use) {
            $namespace_to_use = Utilities::getSystemNamespace();
        }

        App::bind(IThingOwner::class, function () use($namespace_to_use) {
            return $namespace_to_use;
        });
        return $next($request);
    }
}
