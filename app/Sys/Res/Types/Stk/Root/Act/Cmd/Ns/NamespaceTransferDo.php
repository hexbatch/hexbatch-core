<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * todo pop off the live @uses \App\Sys\Res\Types\Stk\Root\Namespace\TransferNamespace
 */
class NamespaceTransferDo extends Act\Cmd\Ns
{
    const UUID = 'fe81b6d9-88ae-44d3-aa7e-790b72e3c68c';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_DO_TRANSFER;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\NamespaceTransfered::class  //after the fact
    ];

}

