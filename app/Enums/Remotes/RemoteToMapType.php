<?php
namespace App\Enums\Remotes;
enum RemoteToMapType : string {

    case DATA = 'data';
    case HEADER = 'header';
    case FILE = 'file';
}
