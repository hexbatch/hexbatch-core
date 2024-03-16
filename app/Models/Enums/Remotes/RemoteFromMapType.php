<?php
namespace App\Models\Enums\Remotes;
enum RemoteFromMapType : string {

    case DATA = 'data';
    case HEADER = 'header';
    case RESPONSE_CODE = 'response_code';

}
