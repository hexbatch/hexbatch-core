<?php

namespace App\Sys\Res\Attributes\Stock\System\Remote;

/*
 remote can have localhost or ip or server domain if the owner of the remote is member of server ns
 */

use App\Sys\Res\Attributes\BaseAttribute;

class RemoteDomain extends BaseAttribute
{
    const UUID = '325d43c0-f258-4d53-9a8a-041bf380f1a5';
    const ATTRIBUTE_NAME = 'remote_domain';
    const PARENT_UUID = RemoteInformation::UUID;

}


