<?php
namespace App\Models\Enums;
enum RemoteUriType : string {

    case NONE = 'none';
    case URL = 'url';
    case SOCKET = 'socket';
    case CONSOLE = 'console';
    case MANUAL = 'manual';
}
