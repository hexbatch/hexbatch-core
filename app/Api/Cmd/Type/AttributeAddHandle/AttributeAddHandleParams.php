<?php
namespace App\Api\Cmd\Type\AttributeAddHandle;


use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\AttributeHandleAdd;


class AttributeAddHandleParams extends AttributeHandleAdd implements IActionParams,IActionOaInput
{
    use SharedAttributeHandleParams;


    public function fromThing(Thing $thing): void
    {

    }


}
