<?php
namespace App\Api\Cmd\Set\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Api\Cmd\Set\SetParams;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\SetPromote;


class SetPromoteParams extends SetPromote implements IActionParams,IActionOaInput
{

    use SetParams;

    public function setupThingData(mixed $thing): void
    {

    }


    public function setupDataWithThing(mixed $thing): void
    {
        // TODO: Implement pushData() method.
    }

    public function processChildrenData(mixed $thing): void {}
}
