<?php

namespace App\Http\Controllers\Web;



use Hexbatch\Things\Models\Thing;

class TestController
{

    public function test() {

//        $me = \App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\TypePublish::resolveAction(action_id: 14);
//        $me->wakeLinkedThings();
        $thing = Thing::getThing(ref_uuid: '97020df1-1953-4402-beb8-1f3d59e9fe7f');
        $thing->pushLeavesToJobs();
    }
}
