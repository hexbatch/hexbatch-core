<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class GiveNamespace extends BaseType
{
    const UUID = '50f106ab-64aa-4628-8770-d525adf1d088';
    const TYPE_NAME = 'api_elsewhere_give_namespace';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\ElsewhereGiveNamespace::class,
    ];

}

