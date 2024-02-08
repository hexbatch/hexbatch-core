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

    // bounds start at 3000

    const BOUND_NOT_FOUND = 3001;
    const BOUND_INVALID_CRON = 3002;
    const BOUND_INVALID_PERIOD = 3003;
    const BOUND_INVALID_START_STOP = 3004;
    const BOUND_CANNOT_EDIT = 3005;
    const BOUND_NEEDS_MIN_INFO = 3006;

    const BOUND_CANNOT_PING = 3007;
    const BOUND_TYPE_DEF = 3008;

    // attributes start at 5000
    const ATTRIBUTE_NOT_FOUND = 5001;
    const ATTRIBUTE_PING_DATA_MISSING = 5002;
    const ATTRIBUTE_CANNOT_BE_USED_AS_PARENT = 5003;
    const ATTRIBUTE_SCHEMA_ISSUE = 5004;

    const ATTRIBUTE_CANNOT_EDIT = 5010;

    //element types start at 6000

    const ELEMENT_TYPE_NOT_FOUND = 6001;

    //elements start at 7000

    const ELEMENT_NOT_FOUND =  7001;


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
        self::BOUND_NOT_FOUND => '',
        self::BOUND_INVALID_CRON => '',
        self::BOUND_INVALID_PERIOD => '',
        self::BOUND_INVALID_START_STOP => '',
        self::BOUND_CANNOT_EDIT => '',
        self::BOUND_NEEDS_MIN_INFO => '',
        self::BOUND_CANNOT_PING => '',
        self::BOUND_TYPE_DEF => '',
        self::ATTRIBUTE_NOT_FOUND => '',
        self::ATTRIBUTE_PING_DATA_MISSING => '',
        self::ATTRIBUTE_CANNOT_BE_USED_AS_PARENT => '',
        self::ATTRIBUTE_SCHEMA_ISSUE => '',
        self::ELEMENT_TYPE_NOT_FOUND => '',
        self::ELEMENT_NOT_FOUND => '',
        self::ATTRIBUTE_CANNOT_EDIT => '',
    ];
}
