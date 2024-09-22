<?php
namespace App\Enums\Remotes;
enum RemoteUriType : string {

    case NONE = 'none';
    case URL = 'url';
    case CONSOLE = 'console';
    case MANUAL_OWNER = 'manual_owner';
    case MANUAL_ELEMENT = 'manual_element';

    case CODE = 'code';

    const MANUAL_TYPES = [
      self::MANUAL_ELEMENT,
      self::MANUAL_OWNER,
    ];

    const SENSITIVE_TYPES = [
        //none at present but keep as placeholder
    ];

    const FORBIDDEN_TYPES = [
        self::CODE,
    ];

    const DISPATCHABLE_TYPES = [
        self::URL,
        self::CONSOLE,
        self::CODE
    ];
}
