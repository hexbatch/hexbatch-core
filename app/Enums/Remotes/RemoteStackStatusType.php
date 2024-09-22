<?php
namespace App\Enums\Remotes;
enum RemoteStackStatusType : string {

    case NONE = 'none';
    case PENDING = 'pending';
    case STARTED = 'started';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case ERROR = 'error';


}
