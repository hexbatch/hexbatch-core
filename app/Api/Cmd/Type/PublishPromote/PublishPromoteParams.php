<?php
namespace App\Api\Cmd\Type\PublishPromote;


use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\TypePublishPromote;


class PublishPromoteParams extends TypePublishPromote implements IActionParams,IActionOaInput
{
    use PublishingParams;

    public function fromThing(Thing $thing): void
    {

    }

    public function pushData(Thing $thing): void
    {
        // TODO: Implement pushData() method.
    }
}
