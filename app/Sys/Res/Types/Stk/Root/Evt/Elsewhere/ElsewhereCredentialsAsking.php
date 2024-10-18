<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * About to ask for new credentials, can be cancelled
 */

class ElsewhereCredentialsAsking extends Evt\ScopeSet
{
    const UUID = 'b6e19a59-36cf-49e2-8001-7bddc792c4f8';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_CREDENTIALS_ASKING;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElsewhere::UUID
    ];

}

