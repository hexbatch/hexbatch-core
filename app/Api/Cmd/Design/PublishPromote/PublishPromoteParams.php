<?php
namespace App\Api\Cmd\Design\PublishPromote;


use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignPublishPromote;


class PublishPromoteParams extends DesignPublishPromote implements IActionParams,IActionOaInput
{
    use PublishingParams;

    public function fromThing(Thing $thing): void
    {

    }

}
