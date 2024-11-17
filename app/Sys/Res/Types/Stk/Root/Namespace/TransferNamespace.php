<?php

namespace App\Sys\Res\Types\Stk\Root\Namespace;

use App\Sys\Res\Types\Stk\Root\NamespaceType;


/**
 * todo this is applied as a live when the user wants to transfer a namespace they own, its added on the @uses \App\Sys\Res\Types\Stk\Root\Api\Namespace\StartTransfer
 */
class TransferNamespace extends NamespaceType
{
    const UUID = '66ff3f14-5676-497c-8946-d6a15b66848a';
    const TYPE_NAME = 'transfer_namesapce';





    const PARENT_CLASSES = [
        NamespaceType::class
    ];

}

