<?php

namespace App\Sys\Res\Types\Stk\Root\NS\System\ThisServer\ThisServerNs;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\PublicType;
use App\Sys\Res\Types\Stk\Root\NS\System\ThisServer\ThisServerNS;


class ThisServerPublic extends BaseType
{
    const UUID = 'da0ea91d-9427-4f6c-990e-34233e3a8f65';
    const TYPE_NAME = 'system_public';


    const PARENT_CLASSES = [
        ThisServerNS::class,
        PublicType::class
    ];

}

