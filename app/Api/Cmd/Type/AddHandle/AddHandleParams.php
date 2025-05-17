<?php
namespace App\Api\Cmd\Type\AddHandle;


use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\TypeHandleAdd;


class AddHandleParams extends TypeHandleAdd implements IActionParams,IActionOaInput
{
    use SharedHandleParams;


    public function setupThingData(mixed $thing): void
    {

    }


    public function setupDataWithThing(mixed $thing): void
    {
        // TODO: Implement pushData() method.
    }

    public function processChildrenData(mixed $thing): void {}
}
