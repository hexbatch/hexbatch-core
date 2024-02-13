<?php
namespace App\Models\Enums\Remotes;
enum CacheStatusType : string {

    case NONE = 'none';
    case CREATED = 'created';
    case NOT_MADE = 'not_made';
    case ERROR = 'error';

}
