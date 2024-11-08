<?php
namespace App\Api\Cmd\Server\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;

use App\Api\Cmd\Server\ServerParams;

use App\Models\Thing;

use App\Sys\Res\Types\Stk\Root\Act\Cmd\Server\ServerPromote;


class ServerPromoteParams extends ServerPromote implements IActionParams,IActionOaInput
{

    use ServerParams;


    public function fromThing(Thing $thing): void
    {
        // todo pull the data from the thing and fill in the data here from the json stored there
    }




}
