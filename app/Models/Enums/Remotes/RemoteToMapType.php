<?php
namespace App\Models\Enums\Remotes;
enum RemoteToMapType : string {

    case NONE = 'none';
    case BASIC_AUTH = 'basic_auth';
    case BEARER_AUTH = 'bearer_auth';
    case DATA = 'data';
    case HEADER = 'header';
}
