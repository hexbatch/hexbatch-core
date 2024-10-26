<?php

namespace App\Sys\Res\Types\Stk\Root\NS\System\ThisServer\ThisServerNs;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\PrivateType;
use App\Sys\Res\Types\Stk\Root\NS\System\ThisServer\ThisServerNS;


class ThisServerPrivate extends BaseType
{
    const UUID = '11103578-95e2-4b32-b95d-f54f4cd8034d';
    const TYPE_NAME = 'system_private';


    const PARENT_CLASSES = [
        ThisServerNS::class,
        PrivateType::class,
    ];

}

