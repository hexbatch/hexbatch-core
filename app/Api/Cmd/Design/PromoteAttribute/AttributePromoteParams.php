<?php
namespace App\Api\Cmd\Design\PromoteAttribute;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignAttributePromote;


class AttributePromoteParams extends DesignAttributePromote implements IActionParams,IActionOaInput
{
    use AttributeParams;

    public function setupThingData(mixed $thing): void
    {

    }

    public function setupDataWithThing(mixed $thing): void
    {
        // TODO: Implement pushData() method.
    }

    public function processChildrenData(mixed $thing): void {}
}
