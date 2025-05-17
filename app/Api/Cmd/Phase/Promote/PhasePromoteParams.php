<?php
namespace App\Api\Cmd\Phase\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;


use App\Api\Cmd\Phase\PhaseParams;

use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph\PhasePromote;


class PhasePromoteParams extends PhasePromote implements IActionParams,IActionOaInput
{

    use PhaseParams;

    public function setupThingData(mixed $thing): void
    {

    }


    public function setupDataWithThing(mixed $thing): void
    {
        // TODO: Implement pushData() method.
    }

    public function processChildrenData(mixed $thing): void {}
}
