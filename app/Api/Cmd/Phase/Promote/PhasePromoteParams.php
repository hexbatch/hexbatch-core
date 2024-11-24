<?php
namespace App\Api\Cmd\Phase\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;


use App\Api\Cmd\Phase\PhaseParams;
use App\Models\Thing;

use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph\PhasePromote;


class PhasePromoteParams extends PhasePromote implements IActionParams,IActionOaInput
{

    use PhaseParams;

    public function fromThing(Thing $thing): void
    {

    }


    public function pushData(Thing $thing): void
    {
        // TODO: Implement pushData() method.
    }
}
