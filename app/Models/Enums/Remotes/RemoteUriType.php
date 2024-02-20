<?php
namespace App\Models\Enums\Remotes;
enum RemoteUriType : string {

    case NONE = 'none';
    case URL = 'url';
    case SOCKET = 'socket';
    case CONSOLE = 'console';
    case MANUAL = 'manual';

    case CODE = 'code';

    const SENSITIVE_TYPES = [
        self::SOCKET,
        self::CONSOLE
    ];

    const FORBIDDEN_TYPES = [
        self::CODE,
    ];

    const DISPATCHABLE_TYPES = [
        self::URL,
        self::SOCKET,
        self::CONSOLE,
    ];
}
