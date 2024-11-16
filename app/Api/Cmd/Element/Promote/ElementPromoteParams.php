<?php
namespace App\Api\Cmd\Element\Promote;

use App\Api\Cmd\Element\BulkElementParams;
use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\ElementPromote;


class ElementPromoteParams extends ElementPromote implements IActionParams,IActionOaInput
{

    use BulkElementParams;

    public function fromThing(Thing $thing): void
    {

    }




}
