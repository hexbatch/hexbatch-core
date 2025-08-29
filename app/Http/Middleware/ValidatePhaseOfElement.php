<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Element;
use App\Models\Phase;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response as CodeOf;



abstract class ValidatePhaseOfElement extends ValidatePhase
{


    /**
     * @param Element $target
     * @return void
     */
    protected  function checkPhase(Phase $phase, $target) {
        if ($target->element_phase_id !== $phase->id) {
            throw new HexbatchNotPossibleException(
                __("msg.element_not_in_phase"
                ,   [
                        'set_phase'=>$target->element_phase->getName(),
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
        $set = $request->route('element');
        if (!$set) {
            throw new HexbatchNotFound(
                __("msg.element_not_given"),
                CodeOf::HTTP_NOT_FOUND,
                RefCodes::ELEMENT_NOT_FOUND);
        }
        if (!$phase instanceof Element) {
            throw new \LogicException("ValidatePhaseOfElement does not see a element in the parameter");
        }
    }
}
