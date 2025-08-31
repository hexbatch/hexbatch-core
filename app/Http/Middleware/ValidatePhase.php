<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Models\Phase;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as CodeOf;


abstract class ValidatePhase
{

    /**
     * See if the owner of the namespace
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        /**
         * @var Phase $phase
         */
        $phase = $request->route('working_phase');
        if (!$phase) {
            throw new HexbatchNotFound(
                __("msg.no_phase_given"),
                CodeOf::HTTP_NOT_FOUND,
                RefCodes::PHASE_NOT_FOUND);
        }
        if (!$phase instanceof Phase) {
            throw new \LogicException("ValidatePhase does not see a phase in the parameter");
        }
        $this->checkPhase(phase: $phase,target: $this->getValidatedTarget(request: $request));
        return $next($request);
    }

    protected abstract function checkPhase(Phase $phase, $target) ;
    protected abstract function getValidatedTarget(Request $request) ;
}
