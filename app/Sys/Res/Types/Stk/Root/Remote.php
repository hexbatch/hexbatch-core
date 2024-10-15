<?php

namespace App\Sys\Res\Types\Stk\Root;



use App\Sys\Res\Atr\Stk\Remote\RemoteData;
use App\Sys\Res\Atr\Stk\Remote\RemoteDataFormat;
use App\Sys\Res\Atr\Stk\Remote\RemoteDomain;
use App\Sys\Res\Atr\Stk\Remote\RemoteHeader;
use App\Sys\Res\Atr\Stk\Remote\RemoteMethod;
use App\Sys\Res\Atr\Stk\Remote\RemotePort;
use App\Sys\Res\Atr\Stk\Remote\RemoteProtocol;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * note: when remote type is created, it has the cache and rules elements for it put in their standard respective set for remotes.
 * when remote called, it is at the closest parent thing that has children that does not call the remote again,
 * And is represented by  a set containing:
 *  the remote element,
 *  its response element, if it responds,
 *  the element of the data to send or sent (multiple rules can build it up),
 *  the element of the cache (shared for common divisions of the cache, if  divided by ns, element, type. Or just one cache element for all remotes of this type)
 *  the element of the rules (one element for all remote calls for this type)
 * These are all read only to the remotes, except for the data to send.
 *
 *

 *
 *

 */
class Remote extends BaseType
{
    const UUID = 'dc9ddf4b-3ac7-4ac8-9bf5-932e32e74b70';
    const TYPE_NAME = 'remote';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        RemoteDataFormat::UUID,
        RemoteHeader::UUID,
        RemoteMethod::UUID,
        RemoteProtocol::UUID,
        RemoteDomain::UUID,
        RemotePort::UUID,
        RemoteData::UUID,
    ];

    const PARENT_UUIDS = [
        Root::UUID
    ];

}

