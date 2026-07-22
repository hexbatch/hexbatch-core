<?php

namespace App\Sys\Res\Types\Stk\Root\Act;


use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Action;


class Cmd extends Action
{
    const UUID = 'f4717906-b735-415d-80d0-6c17d4177595';

    const ACTION_NAME = TypeOfAction::BASE_COMMAND;


    const PARENT_CLASSES = [
        Action::class
    ];

    const ATTRIBUTE_CLASSES = [];

    public static function getHexbatchClassName() :string { return static::ACTION_NAME->value; }


    const EVENT_CLASSES = [];

    public static function getTypeName(): string
    {
        return static::ACTION_NAME->value;
    }


}

