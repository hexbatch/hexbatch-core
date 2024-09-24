<?php

namespace App\Helpers\Standard;


use App\Exceptions\HexbatchInvalidException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Attribute;
use App\Models\User;
//todo redo standard setup because all attributes exists in types only, also add in attribute for expiration_at

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
    const STANDARD_ATTRIBUTE =  'standard_attribute';
    const STANDARD_ATTRIBUTE_INFO =  'info';

    const STANDARD_ATTRIBUTE_NAME =  'name';
    const STANDARD_ATTRIBUTE_DESCRIPTION =  'description';
    const STANDARD_ATTRIBUTE_EMAIL =  'email';
    const STANDARD_ATTRIBUTE_PHONE =  'phone';
    const STANDARD_ATTRIBUTE_ADDRESS =  'address';
    const STANDARD_ATTRIBUTE_MAP_LOCATION =  'map_location';
    const STANDARD_ATTRIBUTE_SHAPE_LOCATION =  'shape_location';
    const STANDARD_ATTRIBUTE_TIMEZONE =  'timezone';

    const STANDARD_ATTRIBUTE_DISPLAY =  'display';
    const STANDARD_ATTRIBUTE_PRIMARY_COLOR =  'primary_color';
    const STANDARD_ATTRIBUTE_SECONDARY_COLOR =  'secondary_color';
    const STANDARD_ATTRIBUTE_BG_COLOR =  'bg_color';
    const STANDARD_ATTRIBUTE_OPACITY =  'opacity';
    const STANDARD_ATTRIBUTE_IMAGE =  'image';
    const STANDARD_ATTRIBUTE_SMALL_THUMB =  'small_thumbnail';
    const STANDARD_ATTRIBUTE_MEDIUM_THUMB =  'medium_thumbnail';


    const UUIDS = [
        self::BASE_ATTRIBUTE_NAME => 'a8f8c420-cbba-41a7-b893-c31c478c97fc',
        self::SYSTEM_NAME => null, //generated at seed time, found in db later
        self::DEFAULT_SERVER_ATTRIBUTE_BASE_UUID => '1fed5be4-c705-4c40-81bc-8c89c6a634ec',
        self::STANDARD_ATTRIBUTE => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
        self::STANDARD_ATTRIBUTE_INFO => '016b2926-ab06-44af-b1c5-81520b39975b',
        self::STANDARD_ATTRIBUTE_NAME => '4a9bda81-980c-4f20-9135-e0d2bd4b905f',
        self::STANDARD_ATTRIBUTE_DESCRIPTION => '152c4fd4-d721-45c2-a75b-96d69992ef89',
        self::STANDARD_ATTRIBUTE_EMAIL => 'e3b2254c-3ebd-44c3-9cfb-406560a12880',
        self::STANDARD_ATTRIBUTE_PHONE => '620b02d6-a9d6-4159-83a4-fd926688bcac',
        self::STANDARD_ATTRIBUTE_ADDRESS => '2b4b34f5-606f-47c0-8797-80f8ec38aed5',
        self::STANDARD_ATTRIBUTE_MAP_LOCATION => 'de7c7ae8-1d8a-4c4a-a0ec-ba80ca21c980',
        self::STANDARD_ATTRIBUTE_SHAPE_LOCATION => 'a1bf270e-a08b-48e3-86ac-29b79ad0ce2b',
        self::STANDARD_ATTRIBUTE_TIMEZONE => '5a188757-81a7-4dc5-87de-a08341a12c91',

        self::STANDARD_ATTRIBUTE_DISPLAY => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
        self::STANDARD_ATTRIBUTE_PRIMARY_COLOR => 'c84278fc-a1be-4dc7-b47b-a24bab2464db',
        self::STANDARD_ATTRIBUTE_SECONDARY_COLOR => 'cfc6feb5-d92b-4db0-8146-058aee99a654',
        self::STANDARD_ATTRIBUTE_BG_COLOR => '3b39ce42-a37f-45ef-9972-21b0592e0f87',
        self::STANDARD_ATTRIBUTE_OPACITY => '8a569174-35a8-4af9-8531-1b07c135c63c',
        self::STANDARD_ATTRIBUTE_IMAGE => '2fa8ff2b-1735-4497-b7e0-83645e42240f',
        self::STANDARD_ATTRIBUTE_SMALL_THUMB => '43e9362d-2bf1-409e-9b87-cc29bf95560c',
        self::STANDARD_ATTRIBUTE_MEDIUM_THUMB => 'fbfff4ce-410f-42a0-9e99-87963b131446'
    ];
    const INFO_ATTRIBUTE_NAMES = [
        self::STANDARD_ATTRIBUTE_NAME ,
        self::STANDARD_ATTRIBUTE_DESCRIPTION ,
        self::STANDARD_ATTRIBUTE_EMAIL ,
        self::STANDARD_ATTRIBUTE_PHONE ,
        self::STANDARD_ATTRIBUTE_ADDRESS ,
        self::STANDARD_ATTRIBUTE_MAP_LOCATION ,
        self::STANDARD_ATTRIBUTE_SHAPE_LOCATION ,
        self::STANDARD_ATTRIBUTE_TIMEZONE
    ];


    const DISPLAY_ATTRIBUTE_NAMES = [
        self::STANDARD_ATTRIBUTE_PRIMARY_COLOR ,
        self::STANDARD_ATTRIBUTE_SECONDARY_COLOR ,
        self::STANDARD_ATTRIBUTE_BG_COLOR ,
        self::STANDARD_ATTRIBUTE_OPACITY ,
        self::STANDARD_ATTRIBUTE_IMAGE ,
        self::STANDARD_ATTRIBUTE_SMALL_THUMB ,
        self::STANDARD_ATTRIBUTE_MEDIUM_THUMB ,
    ];


    public static function getStandardAttributeInfo(string $what) : array  {
        $start = self::DEF[$what]??null;
        if (!$start) {
            throw new \LogicException("No SA matches $what");
        }
        $start['name'] = $what;
        $start['uuid'] = self::UUIDS[$what]??config('hbc.base_attribute_uuid');
        $start['parent_uuid'] = $start['parent']? self::UUIDS[$start['parent']] : null ;
        return $start;
    }


    /**
     * @return Attribute[]
     */
    public static function generateMissingAttributes() : array  {
        $news = [];
        foreach (static::DEF as $key => $da) {
            $b_new = false;
            $what = static::getOrCreateStandardAttribute($key,$b_new);
            if ($b_new) {$news[] = $what; }
        }
        return $news;
    }

    /**
     * @var Attribute[] $attribute_cache
     */
    protected static array $attribute_cache = [];

    public static function getAttributeCache() : array {
        $whats = Attribute::where('is_system',true)->orderBy('id')->get();
        foreach ($whats as $woo) {
            static::$attribute_cache[$woo->ref_uuid] = $woo;
        }
        return static::$attribute_cache;
    }

    public static function getOrCreateStandardAttribute(string $what,bool &$is_new = false) : Attribute {
        $info = static::getStandardAttributeInfo($what);
        $cached = static::getAttributeCache()[$info['uuid']]??null;
        if ($cached) {return $cached;}

        $att = Attribute::where('ref_uuid',$info['uuid'])->first();
        if ($att) {return $att;}
        $is_new = true;
        $att = new Attribute();
        $att->user_id = ($what === static::BASE_ATTRIBUTE_NAME? null: User::getOrCreateSystemUser()->id);
        if ($info['parent']) {
            $att->parent_attribute_id = static::getOrCreateStandardAttribute($info['parent'])->id;
        }
        $att->is_system = true;
        $att->attribute_name = $info['name'];
        $att->save();
        $att->ref_uuid = $info['uuid'];
        $att->save();
        print "made ".$att->attribute_name."\n";
        return $att;

    }

    const DEF = [


        self::BASE_ATTRIBUTE_NAME  => [
            'internal_description' => 'Well know base attribute same on all servers',
            'parent' => null,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::SYSTEM_NAME => [
            'internal_description' => 'Attributes from this server have this as the root ancestor',
            'parent' => self::BASE_ATTRIBUTE_NAME,
            // 'value_type' => AttributeValueType::STRING,

        ],

        self::STANDARD_ATTRIBUTE => [
            'internal_description' => 'Base attribute for all standard attributes',
            'parent' => self::SYSTEM_NAME,
            // 'value_type' => AttributeValueType::STRING,
        ],

        //standard info
        self::STANDARD_ATTRIBUTE_INFO => [
            'internal_description' => 'Attributes having to do with describing',
            'parent' => self::STANDARD_ATTRIBUTE,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_NAME => [
            'internal_description' => 'A name not restricted to the naming rules of the code',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_DESCRIPTION => [
            'internal_description' => 'any length text, or markdown or xml or json',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_EMAIL => [
            'internal_description' => 'vaguely resembles email',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_PHONE => [
            'internal_description' => 'phone numbers',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_ADDRESS => [
            'internal_description' => 'address',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_MAP_LOCATION => [
            'internal_description' => '2d map location',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            // 'value_type' => AttributeValueType::MAP_BOUNDS,
        ],

        self::STANDARD_ATTRIBUTE_SHAPE_LOCATION => [
            'internal_description' => '2d map location',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            // 'value_type' => AttributeValueType::SHAPE_BOUNDS,
        ],

        self::STANDARD_ATTRIBUTE_TIMEZONE => [
            'internal_description' => 'name of the timezone',
            'parent' => self::STANDARD_ATTRIBUTE_INFO,
            // 'value_type' => AttributeValueType::STRING,
        ],




        //standard display
        self::STANDARD_ATTRIBUTE_DISPLAY => [
            'internal_description' => 'Attributes having to do with display',
            'parent' => self::STANDARD_ATTRIBUTE,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_PRIMARY_COLOR => [
            'internal_description' => 'Primary color',
            'parent' => self::STANDARD_ATTRIBUTE_DISPLAY,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_SECONDARY_COLOR => [
            'internal_description' => 'Secondary color',
            'parent' => self::STANDARD_ATTRIBUTE_DISPLAY,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_BG_COLOR => [
            'internal_description' => 'Background color',
            'parent' => self::STANDARD_ATTRIBUTE_DISPLAY,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_OPACITY => [
            'internal_description' => 'Opacity',
            'parent' => self::STANDARD_ATTRIBUTE_DISPLAY,
            // 'value_type' => AttributeValueType::NUMERIC,
        ],

        self::STANDARD_ATTRIBUTE_IMAGE => [
            'internal_description' => 'url to image',
            'parent' => self::STANDARD_ATTRIBUTE_DISPLAY,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_SMALL_THUMB => [
            'internal_description' => 'url to image',
            'parent' => self::STANDARD_ATTRIBUTE_DISPLAY,
            // 'value_type' => AttributeValueType::STRING,
        ],

        self::STANDARD_ATTRIBUTE_MEDIUM_THUMB => [
            'internal_description' => 'url to image',
            'parent' => self::STANDARD_ATTRIBUTE_DISPLAY,
            // 'value_type' => AttributeValueType::STRING,
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

    /** @noinspection PhpUnused */
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
