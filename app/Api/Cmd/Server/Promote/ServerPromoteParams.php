<?php
namespace App\Api\Cmd\Server\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;

use App\Api\Cmd\Server\ServerParams;


use App\Sys\Res\Types\Stk\Root\Act\Cmd\Server\ServerPromote;


class ServerPromoteParams extends ServerPromote implements IActionParams,IActionOaInput
{

    use ServerParams;


    public function setupThingData(mixed $thing): void
    {

    }


    public function setupDataWithThing(mixed $thing): void
    {
        // TODO: Implement pushData() method.
    }

    public function processChildrenData(mixed $thing): void {}
}
