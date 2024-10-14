<?php

namespace App\Sys\Res\Types\Stk\System\Remote;


use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Remote;

/*
  *  if the data is not in json, then the result will convert it to json. The incoming or outgoing remote will have its raw text stored
 *   The raw text is converted by handler here (xml ->json) (plain text -- json) (headers -> json) (response code -> json)
 */

//this is the remote response
class RemoteResponse extends BaseType
{
    const UUID = '574388c8-0bec-4e11-aa62-2008f8e179ef';
    const TYPE_NAME = 'remote_response';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        \App\Sys\Res\Atr\Stk\Remote\RemoteResponse\RemoteResponse::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemoteResponse\HttpResponseCode::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemoteResponse\HttpResponseFail::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemoteResponse\HttpResponseSuccess::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemoteResponse\ResponseBody::UUID,
        \App\Sys\Res\Atr\Stk\Remote\RemoteResponse\ResponseHeaders::UUID,
    ];

    const PARENT_UUIDS = [
        Remote::UUID
    ];

}

