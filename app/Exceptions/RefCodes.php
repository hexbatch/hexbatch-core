<?php

namespace App\Exceptions;

class RefCodes {
    //users start at 1

    const VALIDATION = 1;
    //users start at 1000
    const BAD_LOGIN = 1000;
    const RESOURCE_NAME_UNIQUE_PER_USER = 1002;
    const USER_NOT_FOUND = 1003;
    const USER_NOT_PRIV = 1004;

    //groups start at 2000
    const ONLY_OWNERS_CAN_DELETE_GROUPS = 2001;
    const ONLY_ADMINS_CAN_CHANGE_MEMBERSHIP = 2002;
    const ONLY_OWNERS_CAN_CHANGE_ADMINS = 2003;
    const GROUP_OPERATION_MISSING_MEMBER = 2004;

    const GROUP_NOT_FOUND = 2005;

    //time bounds start at 3000

    const TIME_BOUND_NOT_FOUND = 3001;
    const TIME_BOUND_INVALID_CRON = 3002;
    const TIME_BOUND_INVALID_PERIOD = 3003;
    const TIME_BOUND_INVALID_START_STOP = 3004;
    const TIME_BOUND_CANNOT_EDIT = 3005;
    const TIME_BOUND_NEEDS_MIN_INFO = 3006;

    const URLS = [
        self::BAD_LOGIN => '',
        self::USER_NOT_FOUND => '',
        self::USER_NOT_PRIV => '',
        self::RESOURCE_NAME_UNIQUE_PER_USER => '',
        self::ONLY_OWNERS_CAN_DELETE_GROUPS => '',
        self::ONLY_ADMINS_CAN_CHANGE_MEMBERSHIP => '',
        self::ONLY_OWNERS_CAN_CHANGE_ADMINS => '',
        self::GROUP_OPERATION_MISSING_MEMBER => '',
        self::GROUP_NOT_FOUND => '',
        self::TIME_BOUND_NOT_FOUND => '',
        self::TIME_BOUND_INVALID_CRON => '',
        self::TIME_BOUND_INVALID_PERIOD => '',
        self::TIME_BOUND_INVALID_START_STOP => '',
        self::TIME_BOUND_CANNOT_EDIT => '',
        self::TIME_BOUND_NEEDS_MIN_INFO => '',
    ];
}
