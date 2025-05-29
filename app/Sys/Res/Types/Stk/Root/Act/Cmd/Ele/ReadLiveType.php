<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Things make a child for each attribute to read and then join together in json in parent
 * This is used to find the most recent live and ignore any descendent of same type.
 * If any attribute cannot be read, then this action will fail
 */
class ReadLiveType extends Act\Cmd\Ele
{
    const UUID = '5deecdba-5d2c-4251-9c13-852d7d4743d7';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ_LIVE_TYPE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Pragma::class,
        Act\Cmd\Ele::class,
    ];

    const EVENT_CLASSES = [
        Evt\Set\Reading::class
    ];

}

