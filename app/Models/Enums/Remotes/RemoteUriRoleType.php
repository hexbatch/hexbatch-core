<?php
namespace App\Models\Enums\Remotes;
enum RemoteUriRoleType : string
{

    case READ_AND_WRITE = 'read_and_write';
    case READ = 'read';
    case WRITE = 'write';
    case EVENT_SUCCESS = 'event_success';
    case EVENT_FAIL = 'event_fail';
    case EVENT_ALWAYS = 'event_always';

}
