<?php
namespace App\Api\Cmd\Type\AddHandle;


use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\TypeHandleAdd;


class AddHandleParams extends TypeHandleAdd implements IActionParams,IActionOaInput
{
    use SharedHandleParams;


    public function setupThingData(Thing $thing): void
    {

    }


    public function setupDataWithThing(Thing $thing): void
    {
        // TODO: Implement pushData() method.
    }

    public function processChildrenData( Thing $thing): void {}
}
