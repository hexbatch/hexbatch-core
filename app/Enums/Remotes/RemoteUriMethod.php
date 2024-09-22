<?php
namespace App\Enums\Remotes;
enum RemoteUriMethod : string {

    case NONE = 'none';
    case POST = 'post';
    case GET = 'get';
    case PUT = 'put';
    case PATCH = 'patch';
    case DELETE = 'delete';
}
