<?php
namespace App\Models\Enums\Attributes;
enum AttributeRemoteUsePolicy : string {
    case READ_AND_WRITE_LOCAL = 'read_and_write_local';
    case READ_ONLY_REMOTE_WRITE_LOCAL = 'read_only_remote_write_local';
    case WRITE_ONLY_REMOTE_READ_LOCAL = 'write_only_remote_read_local';
    case READ_AND_WRITE_REMOTE = 'read_and_write_remote';
}



