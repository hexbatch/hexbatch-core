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
        // todo pull the data from the thing and fill in the data here from the json stored there
    }

}
