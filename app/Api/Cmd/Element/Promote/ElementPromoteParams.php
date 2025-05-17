<?php
namespace App\Api\Cmd\Element\Promote;

use App\Api\Cmd\Element\BulkElementParams;
use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\ElementPromote;


class ElementPromoteParams extends ElementPromote implements IActionParams,IActionOaInput
{

    use BulkElementParams;

    public function setupThingData(mixed $thing): void
    {

    }


    public function setupDataWithThing(mixed $thing): void
    {
        // TODO: Implement pushData() method.
    }

    public function processChildrenData(mixed $thing): void {}
}
