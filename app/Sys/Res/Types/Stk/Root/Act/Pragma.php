<?php

namespace App\Sys\Res\Types\Stk\Root\Act;


use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Action;

class Pragma extends BaseType
{
    const UUID = '0990d423-b26d-4191-9cee-3d04464448bc';
    const ACTION_NAME = TypeOfAction::BASE_PRAGMA;

    const ATTRIBUTE_CLASSES = [];


    const PARENT_CLASSES = [
        Action::class
    ];



}

