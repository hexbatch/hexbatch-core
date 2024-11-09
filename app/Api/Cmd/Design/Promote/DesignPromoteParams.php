<?php
namespace App\Api\Cmd\Design\Promote;

use App\Api\Cmd\Design\DesignParams;
use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;

use App\Models\Thing;

use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignPromote;


class DesignPromoteParams extends DesignPromote implements IActionParams,IActionOaInput
{
    use DesignParams;


    public function fromThing(Thing $thing): void
    {

    }


}
