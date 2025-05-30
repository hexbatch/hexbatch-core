<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Models\ActionDatum;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/*
 * Type must have this in up-type for live rules to be used on it
 */
class Handle extends BaseType
{
    const UUID = '10d8097d-9334-40c7-82e2-6e2a7bc0dc3b';
    const TYPE_NAME = 'handle';





    const PARENT_CLASSES = [
        Root::class
    ];

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->is_public_domain = true;
        return $this->action_data;
    }

}

