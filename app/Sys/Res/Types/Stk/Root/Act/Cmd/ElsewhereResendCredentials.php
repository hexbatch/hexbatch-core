<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 *
 */
class ElsewhereResendCredentials extends Act\Cmd
{
    const UUID = '53ec6380-8528-4daf-8375-51858083299e';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_RESEND_CREDENTIALS;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

