<?php

namespace App\Http\Middleware;

use App\Models\Path;
use App\Models\PathPart;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * makes sure the rule belongs to the attribute
 */
class ValidatePartOwnership
{
    /**
     * See if the owner of the namespace
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var Path $path
         */
        $path = $request->route('path');
        if (!$path ) {
            throw new \LogicException("There is no path found in the route when asking for it");
        }
        if (!$path instanceof Path) {
            throw new \LogicException("ValidatePartOwnership does not see an attribute in the parameter");
        }

        /**
         * @var PathPart $part
         */
        $part = $request->route('path_part');
        if (!$part ) {
            throw new \LogicException("There is no path_part found in the route when asking for it");
        }
        if (!$part instanceof PathPart) {
            throw new \LogicException("ValidatePartOwnership does not see a path part in the parameter");
        }
        $this->checkPermission($path,$part);
        return $next($request);
    }

    protected function checkPermission(Path $path,PathPart $part) {
        $part->checkPartOwnership($path);
    }
}
