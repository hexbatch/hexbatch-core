<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class StickElement extends Api\SetApi
{
    const UUID = '4caaa20c-5b9a-440d-8c09-3deeefe54420';
    const TYPE_NAME = 'api_set_stick_element';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\St\SetMemberStick::class,
    ];

}

