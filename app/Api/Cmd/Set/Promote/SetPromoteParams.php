<?php
namespace App\Api\Cmd\Set\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;


use App\Api\Cmd\Set\SetParams;
use App\Models\Thing;

use App\Sys\Res\Types\Stk\Root\Act\Cmd\St\SetPromote;


class SetPromoteParams extends SetPromote implements IActionParams,IActionOaInput
{

    use SetParams;

    public function fromThing(Thing $thing): void
    {

    }




}
