<?php

namespace App\Exceptions;

class RefCodes {
    //users start at 1
    const BAD_LOGIN = 1;
    const RESOURCE_NAME_UNIQUE_PER_USER = 2;
    const USER_NOT_FOUND = 3;

    //groups start at 1000
    const ONLY_OWNERS_CAN_DELETE_GROUPS = 1001;
    const ONLY_ADMINS_CAN_CHANGE_MEMBERSHIP = 1002;
    const ONLY_OWNERS_CAN_CHANGE_ADMINS = 1003;
    const GROUP_OPERATION_MISSING_MEMBER = 1004;

    const GROUP_NOT_FOUND = 1005;

    const URLS = [
        self::BAD_LOGIN => '',
        self::USER_NOT_FOUND => '',
        self::RESOURCE_NAME_UNIQUE_PER_USER => '',
        self::ONLY_OWNERS_CAN_DELETE_GROUPS => '',
        self::ONLY_ADMINS_CAN_CHANGE_MEMBERSHIP => '',
        self::ONLY_OWNERS_CAN_CHANGE_ADMINS => '',
        self::GROUP_OPERATION_MISSING_MEMBER => '',
        self::GROUP_NOT_FOUND => '',
    ];
}
