<?php

namespace App\Sys\Res\Types\Stk\Root\Act;


use App\Enums\Sys\TypeOfAction;

class Cmd extends BaseAction
{
    const UUID = 'f4717906-b735-415d-80d0-6c17d4177595';

    const ACTION_NAME = TypeOfAction::BASE_COMMAND;



    const PARENT_CLASSES = [
        BaseAction::class
    ];

}

