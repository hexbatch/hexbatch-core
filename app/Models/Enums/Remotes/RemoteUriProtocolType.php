<?php
namespace App\Models\Enums\Remotes;
enum RemoteUriProtocolType : string
{

    case NONE = 'none';
    case HTTP = 'http';
    case HTTPS = 'https';
}
