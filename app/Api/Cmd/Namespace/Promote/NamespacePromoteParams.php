<?php
namespace App\Api\Cmd\Namespace\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Api\Cmd\Namespace\NamespaceParams;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\NamespacePromote;


class NamespacePromoteParams extends NamespacePromote implements IActionParams,IActionOaInput
{
    use NamespaceParams;


    public function setupThingData(mixed $thing): void
    {
        //todo get the user id from the thing

    }

    public function setupDataWithThing(mixed $thing): void
    {
        // TODO: Implement pushData() method.
    }

    public function processChildrenData(mixed $thing): void {}
}
