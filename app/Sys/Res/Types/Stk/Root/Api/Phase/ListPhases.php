<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Phase;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListPhases extends BaseType
{
    const UUID = 'fc945e5c-8c7d-43ab-b20c-28c6b122f069';
    const TYPE_NAME = 'api_phase_list_phases';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PhaseApi::class,
        Act\Cmd\Ele\Search::class
    ];

}

