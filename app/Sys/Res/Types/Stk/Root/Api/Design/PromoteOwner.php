<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteOwner extends Api\DesignApi
{
    const UUID = '654736dc-6712-42ce-98ce-2c155e72e326';
    const TYPE_NAME = 'api_design_promote_owner';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignOwnerPromote::class,
    ];

}

