<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ChangeOwner extends Api\DesignApi
{
    const UUID = '1a222e21-c548-4555-95ad-74aee1387f17';
    const TYPE_NAME = 'api_design_change_owner';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignOwnerChange::class,
    ];

}

