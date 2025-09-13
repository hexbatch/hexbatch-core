<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Models\Element;
use App\Models\Phase;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response as CodeOf;



abstract class ValidatePhaseOfLink extends ValidatePhaseOfElement
{


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
            throw new \LogicException("ValidatePhaseOfLink does not see a link in the parameter");
        }
    }
}
