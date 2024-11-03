<?php
namespace App\Api\Thinger\Design\Promote;


use App\Api\IApiOaParams;
use App\Api\Thinger\IApiThingSetup;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Api\Design\Promotion;
use Illuminate\Http\Request;

class DesignPromoteParams extends Promotion implements IApiOaParams, IApiThingSetup
{

    public function fromRequest(Request $request)
    {
        //todo fill in the data here, use the design action class
    }

    public function pushData(Thing $thing): void
    {
        //todo write the data here to the thing, use the design action class
    }


    public function getActions(): array
    {
        //todo return the action class 12
        return [1,2,3];
    }
}
