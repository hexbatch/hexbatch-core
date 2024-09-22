<?php
namespace App\Enums\Remotes;
enum RemoteCachePolicyType : string {

    case NORMAL = 'normal';
    case NOT_USE_CACHE = 'not_use_cache';
    case USE_CACHE_OR_FAIL = 'use_cache_or_fail';

}
