<?php

namespace App\Sys\Res\Types\Stk\Root\Act;


use App\Sys\Res\Types\BaseType;

/**
 * todo (also this) cannot be inherited without permission!
 * @see \App\Models\Thing for notes
 *
 */
class NoEventsTriggered extends BaseType
{
    const UUID = '6108fe35-1f03-419a-8ae5-efdf99f2bf15';
    const TYPE_NAME = 'no_events_triggered';





    const PARENT_CLASSES = [
        BaseAction::class
    ];

}

