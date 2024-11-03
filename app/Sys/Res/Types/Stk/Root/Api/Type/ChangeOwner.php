<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ChangeOwner extends Api\TypeApi
{
    const UUID = 'e5bbf61e-1ff6-4d4b-86a0-2cdfd1e014db';
    const TYPE_NAME = 'api_type_change_owner';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ty\TypeOwnerChange::class,
    ];

}

