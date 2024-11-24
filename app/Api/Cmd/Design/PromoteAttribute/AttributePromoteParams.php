<?php
namespace App\Api\Cmd\Design\PromoteAttribute;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignAttributePromote;


class AttributePromoteParams extends DesignAttributePromote implements IActionParams,IActionOaInput
{
    use AttributeParams;

    public function fromThing(Thing $thing): void
    {

    }

    public function pushData(Thing $thing): void
    {
        // TODO: Implement pushData() method.
    }
}
