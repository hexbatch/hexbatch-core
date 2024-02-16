<?php

namespace App\Helpers\Attributes\Apply;

use App\Exceptions\HexbatchInvalidException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\Attributes\AttributeValueType;
use App\Models\User;



/**
 * Standard attributes are all final, except for the ones that are parents.
 * They are readable by anyone, unless made private in type
 * They have no bounds
 */
class StandardAttributes
{
    const DEFAULT_SERVER_ATTRIBUTE_BASE_UUID = 'default-server-uuid';
    const BASE_ATTRIBUTE_NAME = 'base';
    const SYSTEM_NAME = User::SYSTEM_NAME;
    const BASE_STANDARD_ATTRIBUTE = User::SYSTEM_NAME . '.standard_root';
    const STANDARD_ATTRIBUTE_INFO = User::SYSTEM_NAME . '.info';
    const STANDARD_ATTRIBUTE_NAME = self::STANDARD_ATTRIBUTE_INFO . '_name';
    const STANDARD_ATTRIBUTE_DESCRIPTION = self::STANDARD_ATTRIBUTE_INFO . '_description';
    const STANDARD_ATTRIBUTE_EMAIL = self::STANDARD_ATTRIBUTE_INFO . '_email';
    const STANDARD_ATTRIBUTE_PHONE = self::STANDARD_ATTRIBUTE_INFO . '_phone';
    const STANDARD_ATTRIBUTE_ADDRESS = self::STANDARD_ATTRIBUTE_INFO . '_address';
    const STANDARD_ATTRIBUTE_MAP_LOCATION = self::STANDARD_ATTRIBUTE_INFO . '_map_location';
    const STANDARD_ATTRIBUTE_SHAPE_LOCATION = self::STANDARD_ATTRIBUTE_INFO . '_shape_location';
    const STANDARD_ATTRIBUTE_TIMEZONE = self::STANDARD_ATTRIBUTE_INFO . '_timezone';

    const UUIDS = [
        self::SYSTEM_NAME => null, //generated at seed time, found in db later
        self::BASE_ATTRIBUTE_NAME => 'a8f8c420-cbba-41a7-b893-c31c478c97fc',
        self::DEFAULT_SERVER_ATTRIBUTE_BASE_UUID => '1fed5be4-c705-4c40-81bc-8c89c6a634ec',
        self::BASE_STANDARD_ATTRIBUTE => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
        self::STANDARD_ATTRIBUTE_INFO => '016b2926-ab06-44af-b1c5-81520b39975b',
        self::STANDARD_ATTRIBUTE_NAME => '4a9bda81-980c-4f20-9135-e0d2bd4b905f',
        self::STANDARD_ATTRIBUTE_DESCRIPTION => '152c4fd4-d721-45c2-a75b-96d69992ef89',
        self::STANDARD_ATTRIBUTE_EMAIL => 'e3b2254c-3ebd-44c3-9cfb-406560a12880',
        self::STANDARD_ATTRIBUTE_PHONE => '620b02d6-a9d6-4159-83a4-fd926688bcac',
        self::STANDARD_ATTRIBUTE_ADDRESS => '2b4b34f5-606f-47c0-8797-80f8ec38aed5',
        self::STANDARD_ATTRIBUTE_MAP_LOCATION => 'de7c7ae8-1d8a-4c4a-a0ec-ba80ca21c980',
        self::STANDARD_ATTRIBUTE_SHAPE_LOCATION => 'a1bf270e-a08b-48e3-86ac-29b79ad0ce2b',
        self::STANDARD_ATTRIBUTE_TIMEZONE => '5a188757-81a7-4dc5-87de-a08341a12c91',
    ];

    const INFO_ATTRIBUTE_NAMES = [
        self::STANDARD_ATTRIBUTE_NAME ,
        self::STANDARD_ATTRIBUTE_DESCRIPTION ,
        self::STANDARD_ATTRIBUTE_EMAIL ,
        self::STANDARD_ATTRIBUTE_PHONE ,
        self::STANDARD_ATTRIBUTE_ADDRESS ,
        self::STANDARD_ATTRIBUTE_MAP_LOCATION ,
        self::STANDARD_ATTRIBUTE_SHAPE_LOCATION ,
        self::STANDARD_ATTRIBUTE_TIMEZONE ,
    ];

    public static function getStandardAttributeInfo(string $what) : array  {
        $start = self::DEF[$what]??null;
        if (!$start) {
            throw new \LogicException("No SA matches $what");
        }
        $start['name'] = $what;
        $start['uuid'] = self::UUIDS[$what]??null;
        $start['parent_uuid'] = $start['parent']? self::UUIDS[$start['parent']] : null ;
        return $start;
    }

    const DEF = [

        self::BASE_ATTRIBUTE_NAME => [
            'internal_description' => 'Base attribute for all attributes on all servers. This allows any attribute to be targeted in rules and searches',
            'parent' => null,
            'value_type' => AttributeValueType::STRING,

        ],

        self::SYSTEM_NAME => [
            'internal_description' => 'Base attribute for all attributes made on the server',
            'parent' => self::BASE_ATTRIBUTE_NAME,
            'value_type' => AttributeValueType::STRING,
        ],

        self::BASE_STANDARD_ATTRIBUTE => [
            'internal_description' => 'Base attribute for all standard attributes',
            'parent' => self::SYSTEM_NAME,
            'value_type' => AttributeValueType::STRING,
        ],

        //standard info
        self::STANDARD_ATTRIBUTE_INFO => [
            'internal_description' => 'Attributes having to do with describing',
            'parent' => self::BASE_STANDARD_ATTRIBUTE,
            'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_NAME => [
            'internal_description' => 'A name not restricted to the naming rules of the code',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_DESCRIPTION => [
            'internal_description' => 'any length text, or markdown or xml or json',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_EMAIL => [
            'internal_description' => 'vaguely resembles email',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_PHONE => [
            'internal_description' => 'phone numbers',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_ADDRESS => [
            'internal_description' => 'address',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_MAP_LOCATION => [
            'internal_description' => '2d map location',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            'value_type' => AttributeValueType::MAP_BOUNDS,
        ],

        self::STANDARD_ATTRIBUTE_SHAPE_LOCATION => [
            'internal_description' => '2d map location',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            'value_type' => AttributeValueType::SHAPE_BOUNDS,
        ],

        self::STANDARD_ATTRIBUTE_TIMEZONE => [
            'internal_description' => 'name of the timezone',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            'value_type' => AttributeValueType::STRING,
        ],




        //standard display
        User::SYSTEM_NAME . '.display' => [
            'name' => User::SYSTEM_NAME . '.display',
            'uuid' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'internal_description' => 'Attributes having to do with display',
            'parent' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'value_type' => AttributeValueType::STRING,

        ],
        User::SYSTEM_NAME . '.display.primary_color' => [
            'name' => User::SYSTEM_NAME . '.display.primary_color',
            'uuid' => 'c84278fc-a1be-4dc7-b47b-a24bab2464db',
            'internal_description' => 'Primary color',
            'parent' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
        ],
        User::SYSTEM_NAME . '.display.secondary_color' => [
            'name' => User::SYSTEM_NAME . '.display.secondary_color',
            'uuid' => 'cfc6feb5-d92b-4db0-8146-058aee99a654',
            'internal_description' => 'Secondary color',
            'parent' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
        ],
        User::SYSTEM_NAME . '.display.bg_color' => [
            'name' => User::SYSTEM_NAME . '.display.bg_color',
            'uuid' => '3b39ce42-a37f-45ef-9972-21b0592e0f87',
            'internal_description' => 'Background color',
            'parent' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
        ],
        User::SYSTEM_NAME . '.display.opacity' => [
            'name' => User::SYSTEM_NAME . '.display.opacity',
            'uuid' => '8a569174-35a8-4af9-8531-1b07c135c63c',
            'internal_description' => 'Opacity',
            'parent' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::NUMERIC,
        ],

        User::SYSTEM_NAME . '.display.image' => [
            'name' => User::SYSTEM_NAME . '.display.image',
            'uuid' => '2fa8ff2b-1735-4497-b7e0-83645e42240f',
            'internal_description' => 'url to image',
            'parent' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
        ],
        User::SYSTEM_NAME . '.display.small_thumbnail' => [
            'name' => User::SYSTEM_NAME . '.display.small_thumbnail',
            'uuid' => '43e9362d-2bf1-409e-9b87-cc29bf95560c',
            'internal_description' => 'url to image',
            'parent' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
        ],
        User::SYSTEM_NAME . '.display.medium_thumbnail' => [
            'name' => User::SYSTEM_NAME . '.display.medium_thumbnail',
            'uuid' => 'fbfff4ce-410f-42a0-9e99-87963b131446',
            'internal_description' => 'url to image',
            'parent' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
        ],
        //category
        User::SYSTEM_NAME . '.category' => [
            'name' => User::SYSTEM_NAME . '.category',
            'uuid' => '09dc6180-f48a-4acb-9668-67e08a6d5ea3',
            'internal_description' => 'Base for all standard categories',
            'parent' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'value_type' => AttributeValueType::STRING,

        ],
        User::SYSTEM_NAME . '.category.remote' => [
            'name' => User::SYSTEM_NAME . '.category.remote',
            'uuid' => '618689d9-6dd2-40dc-a288-443d050c71bb',
            'internal_description' => 'Remote Category',
            'parent' => '09dc6180-f48a-4acb-9668-67e08a6d5ea3',
            'value_type' => AttributeValueType::STRING,

        ],
        User::SYSTEM_NAME . '.category.group' => [
            'name' => User::SYSTEM_NAME . '.category.group',
            'uuid' => 'f83aa255-862a-413f-8d9c-083d21c9d983',
            'internal_description' => 'User Group Category',
            'parent' => '09dc6180-f48a-4acb-9668-67e08a6d5ea3',
            'value_type' => AttributeValueType::STRING,

        ],
        User::SYSTEM_NAME . '.category.user' => [
            'name' => User::SYSTEM_NAME . '.category.user',
            'uuid' => '048a58ef-374c-4369-bc9a-fd04210d66a4',
            'internal_description' => 'User category',
            'parent' => '09dc6180-f48a-4acb-9668-67e08a6d5ea3',
            'value_type' => AttributeValueType::STRING,

        ],


        //admin roles
        User::SYSTEM_NAME . '.admin_role' => [
            'name' => User::SYSTEM_NAME . '.admin_role',
            'uuid' => '61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'internal_description' => 'Base for all standard admin roles',
            'parent' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'value_type' => AttributeValueType::NUMERIC,

        ],
        User::SYSTEM_NAME . '.admin_role.view_private_user_info' => [
            'name' => User::SYSTEM_NAME . '.admin_role.view_private_user_info',
            'uuid' => 'be806efe-fffb-4a39-a9ad-6b1b705c2fb1',
            'internal_description' => 'Users with this attribute can view all other user info',
            'parent' => '61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'value_type' => AttributeValueType::NUMERIC,

        ],
        User::SYSTEM_NAME . '.admin_role.set_sensitive_remote_types' => [
            'name' => User::SYSTEM_NAME . '.admin_role.set_sensitive_remote_types',
            'uuid' => 'a202179c-a8af-4f38-a612-7ddb719d4012',
            'internal_description' => 'Users with this attribute can set all the remote types',
            'parent' => '61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'value_type' => AttributeValueType::NUMERIC,

        ],
        User::SYSTEM_NAME . '.admin_role.view_all_remote_activity' => [
            'name' => User::SYSTEM_NAME . '.admin_role.view_all_remote_activity',
            'uuid' => '63b649da-2a3f-4940-8f78-ad8ac3109443',
            'internal_description' => 'Users with this attribute can see all remote activity by all users',
            'parent' => '61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'value_type' => AttributeValueType::NUMERIC,

        ],

        //set relationships
        User::SYSTEM_NAME . '.set_relation' => [
            'name' => User::SYSTEM_NAME . '.set_relation',
            'uuid' => '25197025-96e4-46dc-8b41-433a6336dc9d',
            'internal_description' => 'Base for all standard set relationships',
            'parent' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'value_type' => AttributeValueType::SET,

        ],

        //set types
        User::SYSTEM_NAME . '.set_type' => [
            'name' => User::SYSTEM_NAME . '.set_type',
            'uuid' => '1ea12c69-6fed-4fa7-a816-73326a222b82',
            'internal_description' => 'Base for all standard set types',
            'parent' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'value_type' => AttributeValueType::STRING,

        ],


    ];

    protected static array $checked_key_map = [];
    protected static function getKeyMap() {
        if (count(static::$checked_key_map)) {return static::$checked_key_map;}
        $vals = array_unique(array_values(static::UUIDS));
        if (count($vals) !== count(static::UUIDS)) {
            throw new \LogicException("Duplicated value in the uuid key map");
        }
        static::$checked_key_map = static::UUIDS;

        return static::$checked_key_map;
    }
    public static function getUuid(string $what) {
        $val = static::getKeyMap()[$what]??null;
        if (!$val) {
            throw new \LogicException("No key matches $what");
        }
        return $val;
    }




    public static function validateMapLocation($what, bool $b_throw_exception = true): bool
    {
        $b_ok = true;
        $maybe_coordination = Utilities::toArrayOrNull($what);
        if (!$maybe_coordination) {
            $b_ok = false;
        }
        if ($b_ok) {
            if (
                !array_key_exists('latitude', $maybe_coordination)
                || !array_key_exists('longitude', $maybe_coordination)
                || !is_numeric($maybe_coordination['longitude'] )|| !is_numeric($maybe_coordination['latitude'] )
                || ($maybe_coordination['longitude'] > 180 || $maybe_coordination['longitude'] < -180)
                || ($maybe_coordination['latitude'] > 90 || $maybe_coordination['latitude'] < -90)
            ) {
                $b_ok = false;
            }
        }

        if (!$b_ok) {
            if ($b_throw_exception) {
                throw new HexbatchInvalidException(__("msg.not_map_coordinate"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::MAP_COORDINATE_ISSUE);
            }
            return false;
        }
        return true;
    }

    public static function validateShapeLocation($what, bool $b_throw_exception = true): bool
    {
        $b_ok = true;
        $maybe_coordination = Utilities::toArrayOrNull($what);
        if (!$maybe_coordination) {
            $b_ok = false;
        }
        if ($b_ok) {
            if (
                !array_key_exists('x',$maybe_coordination )
                || !array_key_exists('y',$maybe_coordination )
                || !array_key_exists('z',$maybe_coordination )
                || !is_numeric($maybe_coordination['x'] )|| !is_numeric($maybe_coordination['y'] ) || !is_numeric($maybe_coordination['z'] )
            ) {
                $b_ok = false;
            }
        }

        if (!$b_ok) {
            if ($b_throw_exception) {
                throw new HexbatchInvalidException(__("msg.not_shape_coordinate"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::SHAPE_COORDINATE_ISSUE);
            }
            return false;
        }
        return true;
    }

    public static function validateTimezone($what): void
    {

        if (is_string($what)) {
            foreach (timezone_abbreviations_list() as $zone) {
                foreach ($zone as $item) {
                    if ($item["timezone_id"] === $what) {
                        return;
                    }
                }
            }
        }

        throw new HexbatchInvalidException(__("msg.not_timezone"),
            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
            RefCodes::TIMEZONE_ISSUE);
    }









}
