<?php

namespace App\Sys\Res\Types\Stk\Root\Remote;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Remote;

/*
  *  only url domains that are not localhost can be made as remote calls, ip domains are not allowed.
 *  remote attributes split up the different parts of the url,
 *    domain for example only allows periods for punctuation
 *    the port is numeric only
 *    and the schema is only http or https
 */
class Outgoing extends BaseType
{
    const UUID = 'ef367400-78fc-4460-a71c-3cf34c8e339d';
    const TYPE_NAME = 'remote_outgoing';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Remote::UUID
    ];

}

