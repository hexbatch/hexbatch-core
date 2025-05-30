<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Models\ActionDatum;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/*
 * This is used when a type is made for a set definer in the sys
 */

class Container extends BaseType
{
    const UUID = '51e2fe0a-0087-4315-8324-fc9070a7d41d';
    const TYPE_NAME = 'container';





    const PARENT_CLASSES = [
        Root::class
    ];

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->is_public_domain = true;
        return $this->action_data;
    }

}

