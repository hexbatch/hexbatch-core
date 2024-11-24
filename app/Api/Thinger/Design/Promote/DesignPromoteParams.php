<?php
namespace App\Api\Thinger\Design\Promote;


use App\Api\BaseParams;
use App\Api\IApiOaParams;
use App\Api\Thinger\IApiThingSetup;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Api\Design\Promotion;

use Illuminate\Support\Collection;

class DesignPromoteParams extends Promotion implements IApiOaParams, IApiThingSetup
{
    use BaseParams;

    public function fromCollection(Collection $collection)
    {
        //todo make a new action param for each action, and have them be class members, and call their from collection
    }



    public function pushData(Thing $thing): void
    {
        /*
         *todo The api params will write to the thing data, if it wants to, and then for each of its member actions make a new thing child or descendant, and
         * call that action's params pushData so it can do its setup data,
         * the caller will then change  the leafs as pending and all else as waiting on child
         *

         */
    }


    public function getActions(): array
    {
        //todo return the action class 12
        return [1,2,3];
    }


}
