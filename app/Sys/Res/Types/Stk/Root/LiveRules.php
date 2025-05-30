<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Models\ActionDatum;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/*
 * Type must have this in up-type for live rules to be used on it
 */
class LiveRules extends BaseType
{
    const UUID = '92c139b6-c99a-4213-b992-e8075517c785';
    const TYPE_NAME = 'live_rules';





    const PARENT_CLASSES = [
        Root::class
    ];

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->is_public_domain = true;
        return $this->action_data;
    }

}

