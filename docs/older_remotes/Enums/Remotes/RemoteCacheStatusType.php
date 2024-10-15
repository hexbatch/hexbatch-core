<?php
namespace Remotes;
enum RemoteCacheStatusType : string {

    case NONE = 'none';
    case CREATED = 'created';
    case NOT_MADE = 'not_made';
    case ERROR = 'error';

}
