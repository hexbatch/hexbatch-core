<?php
namespace App\Models\Enums;
enum RemoteInputMapType : string {

    case NONE = 'none';
    case DATA = 'data';
    case HEADER = 'header';
    case RESPONSE_CODE = 'response_code';
}
