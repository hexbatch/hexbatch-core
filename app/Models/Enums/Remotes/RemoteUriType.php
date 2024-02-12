<?php
namespace App\Models\Enums\Remotes;
enum RemoteUriType : string {

    case NONE = 'none';
    case URL = 'url';
    case SOCKET = 'socket';
    case CONSOLE = 'console';
    case MANUAL = 'manual';
    case ACTION_EVENT = 'action_event';

    const SENSITIVE_TYPES = [
        self::SOCKET,
        self::CONSOLE
    ];

    const DISPATCHABLE_TYPES = [
        self::URL,
        self::SOCKET,
        self::CONSOLE,
        self::ACTION_EVENT
    ];
}
