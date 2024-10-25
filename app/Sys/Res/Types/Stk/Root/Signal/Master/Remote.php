<?php

namespace App\Sys\Res\Types\Stk\Root\Signal\Master;



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



    const ATTRIBUTE_CLASSES = [
        \App\Sys\Res\Atr\Stk\Remote\RemoteDataFormat::class,
        \App\Sys\Res\Atr\Stk\Remote\RemoteHeader::class,
        \App\Sys\Res\Atr\Stk\Remote\RemoteMethod::class,
        \App\Sys\Res\Atr\Stk\Remote\RemoteProtocol::class,
        \App\Sys\Res\Atr\Stk\Remote\RemoteDomain::class,
        \App\Sys\Res\Atr\Stk\Remote\RemotePort::class,
        \App\Sys\Res\Atr\Stk\Remote\RemoteData::class,
        \App\Sys\Res\Atr\Stk\Remote\RemoteResponse::class,
        \App\Sys\Res\Atr\Stk\Remote\HttpResponseCode::class,
        \App\Sys\Res\Atr\Stk\Remote\HttpResponseFail::class,
        \App\Sys\Res\Atr\Stk\Remote\HttpResponseSuccess::class,
        \App\Sys\Res\Atr\Stk\Remote\ResponseBody::class,
        \App\Sys\Res\Atr\Stk\Remote\ResponseHeaders::class,
    ];

    const PARENT_CLASSES = [
        MasterSemaphore::class,
    ];

}

