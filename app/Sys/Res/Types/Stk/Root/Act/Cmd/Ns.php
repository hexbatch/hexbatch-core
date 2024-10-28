<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\BaseAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Ns extends BaseAction
{
    const UUID = '413a6ee8-3486-451e-8cbc-69f6a898d639';
    const ACTION_NAME = TypeOfAction::BASE_NAMESPACE;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Cmd::class
    ];



}

