<?php
namespace App\Api\Calls\Design\Promote;


use App\Api\BaseParams;
use App\Api\Calls\IApiThingSetup;
use App\Api\IApiOaParams;
use App\Helpers\Actions\ActionNode;
use App\Sys\Res\Types\Stk\Root\Api\Design\Promotion;
use Illuminate\Support\Collection;

class DesignPromoteParams extends Promotion implements IApiOaParams, IApiThingSetup
{
    use BaseParams;

    public function fromCollection(Collection $collection)
    {

    }



    public function setupDataWithThing( $thing, $params): void
    {

    }


    /**
     * @return ActionNode[]
     */
    public function getActions(): array
    {

        return [1,2,3];
    }


}
