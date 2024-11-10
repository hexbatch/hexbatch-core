<?php
namespace App\Api\Cmd\Element\PromoteEdit;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\ElementPromoteEdit;


class ElementEditPromoteParams extends ElementPromoteEdit implements IActionParams,IActionOaInput
{

    use EditElementParams;

    public function fromThing(Thing $thing): void
    {

    }




}
