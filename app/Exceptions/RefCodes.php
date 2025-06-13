<?php

namespace App\Exceptions;

class RefCodes {
    //general stuff start at 1

    const int VALIDATION = 1;
    const int JSON_ISSUE = 3;
    const int MAP_COORDINATE_ISSUE = 4;
    const int TIMEZONE_ISSUE = 7;
    const int JSON_PATH_ISSUE = 8;
    const int GEO_JSON_ISSUE = 9;
    const int INVALID_UUID = 10;
    const int INVALID_TIME = 11;


    //servers start at 500
    const int SERVER_NOT_FOUND = 500;
    const int SERVER_SCHEMA_ISSUE = 502;


    const int RESOURCE_CANNOT_DELETE_IN_USE = 510;


    //users start at 1000
    const int BAD_LOGIN = 1000;
    const int BAD_REGISTRATION = 1001;
    const int USER_NOT_FOUND = 1003;
    const int USER_NOT_PRIV = 1004;
    const int NO_NAMESPACE = 1005;

    // namespaces start at 2000
    const int NAMESPACE_NOT_FOUND = 2003;
    const int NAMESPACE_NOT_OWNER = 2004;
    const int NAMESPACE_NOT_ADMIN = 2005;
    const int NAMESPACE_NOT_MEMBER = 2008;
    const int NAMESPACE_MEMBER_MISSING_ISSUE = 2006;
    const int NAMESPACE_CANNOT_DELETE_CORE_PARTS = 2007;
    const int NAMESPACE_SCHEMA_ISSUE = 2010;
    const int NAMESPACE_NOT_DEFAULT_OWNER = 2011;


    // bounds start at 3000

    const int BOUND_NOT_FOUND = 3001;
    const int BOUND_INVALID_CRON = 3002;
    const int BOUND_INVALID_PERIOD = 3003;
    const int BOUND_INVALID_START_STOP = 3004;
    const int BOUND_NEEDS_MIN_INFO = 3006;



    const int BOUND_CANNOT_PING = 3007;
    const int BOUND_TYPE_DEF = 3008;

    const int BOUND_INVALID_NAME = 3010;


    const int BOUND_IN_USE = 3020;

    // attributes start at 4000
    const int ATTRIBUTE_NOT_FOUND = 4001;
    const int ATTRIBUTE_PING_DATA_MISSING = 4002;
    const int ATTRIBUTE_CANNOT_BE_USED_AS_PARENT = 4003;
    const int ATTRIBUTE_SCHEMA_ISSUE = 4004;

    const int ATTRIBUTE_CANNOT_EDIT = 4010;
    const int ATTRIBUTE_CANNOT_CLONE = 4011;
    const int ATTRIBUTE_CANNOT_DELETE = 4012;


    const int RULE_SCHEMA_ISSUE = 4100;
    const int RULE_NOT_FOUND = 4101;
    const int RULE_CANNOT_DELETE = 4102;

    const int RULE_CANNOT_EDIT = 4103;









    //element types start at 7000

    const int TYPE_NOT_FOUND = 7001;
    const int TYPE_ONLY_OWNER_CAN_DELETE = 7004;
    const int TYPE_CANNOT_DELETE = 7005;

    const int TYPE_INVALID_NAME = 7006;
    const int TYPE_SCHEMA_ISSUE = 7007;
    const int TYPE_CANNOT_INHERIT = 7008;

    const int TYPE_CANNOT_EDIT = 7010;
    const int TYPE_NEEDS_PUBLISHING = 7100;
    const int TYPE_ALREADY_PUBLISHED = 7101;
    const int TYPE_CANNOT_PUBLISH_ABSTRACT = 7102;

    const int TYPE_PARENT_DENIED_DESIGN = 7200;
    const int TYPE_PARENT_DENIED_PUBLISHING = 7250;

    const int TYPE_GIVEN_ZERO_TO_MAKE = 7300;
    const int TYPE_ALREADY_HAS_OWNER = 7400;

    //elements start at 8000

    const int ELEMENT_NOT_FOUND =  8001;
    const int ELEMENT_BAD_SCHEMA =  8002;
    const int ELEMENT_NOT_IN_SET =  8003;
    const int ELEMENTS_NOT_LISTED_TO_GIVE =  8010;
    const int ELEMENTS_NOT_LISTED_TO_DESTROY =  8011;

    //sets start at 9000
    const int SET_NOT_FOUND =  9001;
    const int SET_SCHEMA_ISSUE =  9002;

    // paths start at 10000
    const int PATH_NOT_FOUND =  10001;
    const int PATH_CANNOT_EDIT =  10002;
    const int PATH_BAD_NAME =  10003;

    const int PATH_SCHEMA_ISSUE = 10004;


    const int PHASE_NOT_FOUND = 20001;
    const int PHASE_IS_DIFFERENT = 20020;


    const int DESIGN_API_SCHEMA_ISSUE = 50000;

    /**
     * @type string[]
     */
    const array URLS = [
        self::JSON_ISSUE => '',
        self::MAP_COORDINATE_ISSUE => '',
        self::TIMEZONE_ISSUE => '',
        self::JSON_PATH_ISSUE => '',
        self::GEO_JSON_ISSUE => '',
        self::INVALID_UUID => '',
        self::INVALID_TIME => '',

        self::SERVER_NOT_FOUND => '',
        self::RESOURCE_CANNOT_DELETE_IN_USE => '',

        self::BAD_LOGIN => '',
        self::BAD_REGISTRATION => '',
        self::USER_NOT_FOUND => '',
        self::USER_NOT_PRIV => '',
        self::NO_NAMESPACE => '',

        self::NAMESPACE_NOT_FOUND => '',
        self::NAMESPACE_NOT_OWNER => '',
        self::NAMESPACE_NOT_ADMIN => '',
        self::NAMESPACE_NOT_MEMBER => '',
        self::NAMESPACE_MEMBER_MISSING_ISSUE => '',
        self::NAMESPACE_CANNOT_DELETE_CORE_PARTS => '',
        self::NAMESPACE_SCHEMA_ISSUE => '',



        self::BOUND_NOT_FOUND => '',
        self::BOUND_INVALID_CRON => '',
        self::BOUND_INVALID_PERIOD => '',
        self::BOUND_INVALID_START_STOP => '',
        self::BOUND_NEEDS_MIN_INFO => '',
        self::BOUND_CANNOT_PING => '',
        self::BOUND_TYPE_DEF => '',
        self::BOUND_INVALID_NAME => '',


        self::ATTRIBUTE_NOT_FOUND => '',
        self::ATTRIBUTE_PING_DATA_MISSING => '',
        self::ATTRIBUTE_CANNOT_BE_USED_AS_PARENT => '',
        self::ATTRIBUTE_SCHEMA_ISSUE => '',
        self::ATTRIBUTE_CANNOT_EDIT => '',
        self::ATTRIBUTE_CANNOT_CLONE => '',
        self::ATTRIBUTE_CANNOT_DELETE => '',

        self::RULE_SCHEMA_ISSUE => '',
        self::RULE_NOT_FOUND => '',
        self::RULE_CANNOT_DELETE => '',
        self::RULE_CANNOT_EDIT => '',





        self::TYPE_NOT_FOUND => '',
        self::TYPE_ONLY_OWNER_CAN_DELETE => '',
        self::TYPE_CANNOT_DELETE => '',
        self::TYPE_INVALID_NAME => '',
        self::TYPE_SCHEMA_ISSUE => '',
        self::TYPE_CANNOT_INHERIT => '',
        self::TYPE_CANNOT_EDIT => '',
        self::TYPE_NEEDS_PUBLISHING => '',
        self::TYPE_ALREADY_PUBLISHED => '',
        self::TYPE_CANNOT_PUBLISH_ABSTRACT => '',
        self::TYPE_PARENT_DENIED_DESIGN => '',
        self::TYPE_PARENT_DENIED_PUBLISHING => '',
        self::TYPE_GIVEN_ZERO_TO_MAKE => '',
        self::TYPE_ALREADY_HAS_OWNER => '',

        self::ELEMENT_NOT_FOUND => '',
        self::ELEMENT_BAD_SCHEMA => '',
        self::ELEMENT_NOT_IN_SET => '',
        self::ELEMENTS_NOT_LISTED_TO_GIVE => '',
        self::ELEMENTS_NOT_LISTED_TO_DESTROY => '',

        self::SET_NOT_FOUND => '',
        self::SET_SCHEMA_ISSUE => '',

        self::PATH_NOT_FOUND => '',
        self::PATH_CANNOT_EDIT => '',
        self::PATH_BAD_NAME => '',

        self::PHASE_NOT_FOUND => '',
        self::PHASE_IS_DIFFERENT => '',

        self::DESIGN_API_SCHEMA_ISSUE => '',

    ];
}
