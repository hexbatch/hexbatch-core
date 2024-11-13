<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Purge extends Api\DesignApi
{
    const UUID = '114814fe-69dd-464c-b79c-fef498423347';
    const TYPE_NAME = 'api_design_purge';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignPurge::class,
    ];

}

