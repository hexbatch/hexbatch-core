<?php
namespace App\Models\Enums\Remotes;
enum RemoteToSourceType : string {

    case FROM_DATA = 'from_data';
    case FROM_STACK = 'from_stack';
}
