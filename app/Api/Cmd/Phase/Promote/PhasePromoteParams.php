<?php
namespace App\Api\Cmd\Phase\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;


use App\Api\Cmd\Phase\PhaseParams;
use App\Api\Cmd\Set\SetParams;
use App\Models\Thing;

use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph\PhasePromote;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\St\SetPromote;


class PhasePromoteParams extends PhasePromote implements IActionParams,IActionOaInput
{

    use PhaseParams;

    public function fromThing(Thing $thing): void
    {

    }




}
