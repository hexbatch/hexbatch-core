<?php
namespace App\Models\Enums\Remotes;
enum RemoteStatusType : string {

    case NONE = 'none';
    case PENDING = 'pending';
    case STARTED = 'started';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CACHED = 'cached';


}
