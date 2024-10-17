<?php

namespace App\Sys\Res\Types\Stk\Root\Signal\Master;



use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Signal\MasterSemaphore;

/**
* make new type with master to have the remote called when the
 * @uses \App\Sys\Res\Types\Stk\Root\Act\Cmd\SemaphoreMasterRun is called
 * and have the
 * @uses MasterResponse derived type use the attributes here
 */
class Remote extends BaseType
{
    const UUID = 'dc9ddf4b-3ac7-4ac8-9bf5-932e32e74b70';
    const TYPE_NAME = 'remote';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        \App\Sys\Res\Atr\Stk\Remote\RemoteDataFormat::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemoteHeader::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemoteMethod::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemoteProtocol::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemoteDomain::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemotePort::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemoteData::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemoteResponse::UUID,
        \App\Sys\Res\Atr\Stk\Remote\HttpResponseCode::UUID,
        \App\Sys\Res\Atr\Stk\Remote\HttpResponseFail::UUID,
        \App\Sys\Res\Atr\Stk\Remote\HttpResponseSuccess::UUID,
        \App\Sys\Res\Atr\Stk\Remote\ResponseBody::UUID,
        \App\Sys\Res\Atr\Stk\Remote\ResponseHeaders::UUID,
    ];

    const PARENT_UUIDS = [
        MasterSemaphore::UUID,
    ];

}

