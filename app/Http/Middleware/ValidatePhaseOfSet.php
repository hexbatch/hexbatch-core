<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\ElementSet;
use App\Models\Phase;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response as CodeOf;



abstract class ValidatePhaseOfSet extends ValidatePhase
{


    /**
     * @param ElementSet $target
     * @return void
     */
    protected  function checkPhase(Phase $phase, $target) {
        if ($target->defining_element->element_phase_id !== $phase->id) {
            throw new HexbatchNotPossibleException(
                __("msg.set_not_in_phase"
                ,   [
                        'set_phase'=>$target->defining_element->element_phase->getName(),
                        'other_phase'=>$phase->getName(),
                    ]
                ),
                CodeOf::HTTP_NOT_FOUND,
                RefCodes::SET_NOT_FOUND);
        }
    }
    protected  function getValidatedTarget(Request $request) {
        /**
         * @var Phase $phase
         */
        $set = $request->route('element_set');
        if (!$set) {
            throw new HexbatchNotFound(
                __("msg.set_not_given"),
                CodeOf::HTTP_NOT_FOUND,
                RefCodes::SET_NOT_FOUND);
        }
        if (!$phase instanceof ElementSet) {
            throw new \LogicException("ValidatePhaseOfSet does not see a set in the parameter");
        }
    }
}
