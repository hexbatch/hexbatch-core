<?php

namespace App\Sys\Res\Types\Stk\Root\Signal\Semaphore\Master;



use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Signal\Semaphore\MasterSemaphore;

/**
* make new type with master to have the remote called when the
 * @uses \App\Sys\Res\Types\Stk\Root\Act\Cmd\Wa\SemaphoreMasterRun is called
 * and have the
 * @uses MasterResponse derived type use the attributes here
 */
class Remote extends BaseType
{
    const UUID = 'dc9ddf4b-3ac7-4ac8-9bf5-932e32e74b70';
    const TYPE_NAME = 'remote';



    const ATTRIBUTE_CLASSES = [
        \App\Sys\Res\Atr\Stk\Signal\Master\RemoteInformation::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\RemoteDataFormat::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\RemoteHeader::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\RemoteMethod::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\RemoteProtocol::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\RemoteDomain::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\RemotePort::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\RemoteData::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\RemoteResponse::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\HttpResponseCode::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\HttpResponseFail::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\HttpResponseSuccess::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\ResponseBody::class,
        \App\Sys\Res\Atr\Stk\Signal\Master\Remote\ResponseHeaders::class,
    ];

    const PARENT_CLASSES = [
        MasterSemaphore::class,
    ];

}

