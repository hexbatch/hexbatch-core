<?php
namespace App\Api\Cmd\Type\PublishPromote;


use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\TypePublishPromote;


class PublishPromoteParams extends TypePublishPromote implements IActionParams,IActionOaInput
{
    use PublishingParams;

    public function setupThingData(Thing $thing): void
    {

    }

    public function setupDataWithThing(Thing $thing): void
    {
        // TODO: Implement pushData() method.
    }

    public function processChildrenData( Thing $thing): void {}
}
