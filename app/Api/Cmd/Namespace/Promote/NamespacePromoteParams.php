<?php
namespace App\Api\Cmd\Namespace\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Api\Cmd\Namespace\NamespaceParams;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\NamespacePromote;


class NamespacePromoteParams extends NamespacePromote implements IActionParams,IActionOaInput
{
    use NamespaceParams;


    public function fromThing(Thing $thing): void
    {

    }

    public function pushData(Thing $thing): void
    {
        // TODO: Implement pushData() method.
    }
}
