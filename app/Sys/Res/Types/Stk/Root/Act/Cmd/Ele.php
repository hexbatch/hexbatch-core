<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Ele extends Cmd
{
    const UUID = 'ba8f74d3-ecfc-4dab-b56c-075e9c004023';
    const ACTION_NAME = TypeOfAction::BASE_ELEMENT;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Cmd::class
    ];



}

