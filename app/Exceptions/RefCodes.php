<?php

namespace App\Exceptions;

class RefCodes {
    //general stuff start at 1

    const VALIDATION = 1;
    const JSON_ISSUE = 3;
    const MAP_COORDINATE_ISSUE = 4;
    const TIMEZONE_ISSUE = 7;
    const JSON_PATH_ISSUE = 8;
    const GEO_JSON_ISSUE = 9;

    const SHAPE_COORDINATE_ISSUE = 12;

    //servers start at 500
    const SERVER_NOT_FOUND = 500;


    //users start at 1000
    const BAD_LOGIN = 1000;
    const RESOURCE_NAME_UNIQUE_PER_USER = 1002;
    const USER_NOT_FOUND = 1003;
    const USER_NOT_PRIV = 1004;

    // user types start at 1500
    const USER_TYPE_NOT_FOUND = 1503;

    //groups start at 2000
    const ONLY_OWNERS_CAN_DELETE_GROUPS = 2001;
    const ONLY_ADMINS_CAN_CHANGE_MEMBERSHIP = 2002;
    const ONLY_OWNERS_CAN_CHANGE_ADMINS = 2003;
    const GROUP_OPERATION_MISSING_MEMBER = 2004;
    const GROUP_INVALID_NAME = 2005;

    const GROUP_NOT_FOUND = 2006;

    // bounds start at 3000

    const BOUND_NOT_FOUND = 3001;
    const BOUND_INVALID_CRON = 3002;
    const BOUND_INVALID_PERIOD = 3003;
    const BOUND_INVALID_START_STOP = 3004;
    const BOUND_CANNOT_EDIT = 3005;
    const BOUND_NEEDS_MIN_INFO = 3006;


    const BOUND_CANNOT_PING = 3007;
    const BOUND_TYPE_DEF = 3008;

    const BOUND_INVALID_NAME = 3010;

    // attributes start at 4000
    const ATTRIBUTE_NOT_FOUND = 4001;
    const ATTRIBUTE_PING_DATA_MISSING = 4002;
    const ATTRIBUTE_CANNOT_BE_USED_AS_PARENT = 4003;
    const ATTRIBUTE_SCHEMA_ISSUE = 4004;
    const ATTRIBUTE_BAD_NAME = 4005;



    const ATTRIBUTE_CANNOT_EDIT = 4010;
    const ATTRIBUTE_CANNOT_CLONE = 4011;
    const ATTRIBUTE_CANNOT_DELETE = 4012;

    const RULE_SCHEMA_ISSUE = 4100;
    const RULE_NOT_FOUND = 4101;

    //remotes start at 5000

    const REMOTE_NOT_FOUND = 5001;
    const REMOTE_SCHEMA_ISSUE = 5010;
    const REMOTE_ACTIVITY_NOT_FOUND =  5015;
    const REMOTE_UNCALLABLE =  5050;
    const REMOTE_STACK_NOT_FOUND =  5200;

    //actions start at 6000

    const ACTION_NOT_FOUND =  6001;

    //element types start at 7000

    const ELEMENT_TYPE_NOT_FOUND = 7001;
    const ELEMENT_TYPE_NOT_AUTHORIZED = 7002;
    const ELEMENT_TYPE_INVALID_GROUP = 7003;
    const ELEMENT_TYPE_ONLY_OWNER_CAN_DELETE = 7004;
    const ELEMENT_TYPE_CANNOT_DELETE = 7005;
    const ELEMENT_TYPE_INVALID_NAME = 7006;
    const ELEMENT_TYPE_BAD_SCHEMA = 7007;
    const ELEMENT_TYPE_CANNOT_INHERIT = 7008;

    //elements start at 8000

    const ELEMENT_NOT_FOUND =  8001;

    //sets start at 9000


    const URLS = [
        self::JSON_ISSUE => '',
        self::MAP_COORDINATE_ISSUE => '',
        self::TIMEZONE_ISSUE => '',
        self::JSON_PATH_ISSUE => '',
        self::GEO_JSON_ISSUE => '',

        self::SERVER_NOT_FOUND => '',

        self::BAD_LOGIN => '',
        self::USER_NOT_FOUND => '',
        self::USER_NOT_PRIV => '',

        self::USER_TYPE_NOT_FOUND => '',


        self::RESOURCE_NAME_UNIQUE_PER_USER => '',
        self::ONLY_OWNERS_CAN_DELETE_GROUPS => '',
        self::ONLY_ADMINS_CAN_CHANGE_MEMBERSHIP => '',
        self::ONLY_OWNERS_CAN_CHANGE_ADMINS => '',
        self::GROUP_OPERATION_MISSING_MEMBER => '',
        self::GROUP_NOT_FOUND => '',
        self::GROUP_INVALID_NAME => '',

        self::BOUND_NOT_FOUND => '',
        self::BOUND_INVALID_CRON => '',
        self::BOUND_INVALID_PERIOD => '',
        self::BOUND_INVALID_START_STOP => '',
        self::BOUND_CANNOT_EDIT => '',
        self::BOUND_NEEDS_MIN_INFO => '',
        self::BOUND_CANNOT_PING => '',
        self::BOUND_TYPE_DEF => '',
        self::BOUND_INVALID_NAME => '',


        self::ATTRIBUTE_NOT_FOUND => '',
        self::ATTRIBUTE_PING_DATA_MISSING => '',
        self::ATTRIBUTE_CANNOT_BE_USED_AS_PARENT => '',
        self::ATTRIBUTE_SCHEMA_ISSUE => '',
        self::ATTRIBUTE_CANNOT_EDIT => '',
        self::ATTRIBUTE_BAD_NAME => '',
        self::ATTRIBUTE_CANNOT_CLONE => '',
        self::ATTRIBUTE_CANNOT_DELETE => '',

        self::RULE_SCHEMA_ISSUE => '',

        self::REMOTE_NOT_FOUND => '',
        self::REMOTE_SCHEMA_ISSUE => '',
        self::REMOTE_ACTIVITY_NOT_FOUND => '',
        self::REMOTE_STACK_NOT_FOUND => '',


        self::ACTION_NOT_FOUND => '',

        self::ELEMENT_TYPE_NOT_FOUND => '',
        self::ELEMENT_TYPE_NOT_AUTHORIZED => '',
        self::ELEMENT_TYPE_INVALID_GROUP => '',
        self::ELEMENT_TYPE_ONLY_OWNER_CAN_DELETE => '',
        self::ELEMENT_TYPE_CANNOT_DELETE => '',
        self::ELEMENT_TYPE_INVALID_NAME => '',
        self::ELEMENT_TYPE_BAD_SCHEMA => '',
        self::ELEMENT_TYPE_CANNOT_INHERIT => '',

        self::ELEMENT_NOT_FOUND => '',

    ];
}
