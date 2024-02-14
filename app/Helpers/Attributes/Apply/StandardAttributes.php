<?php

namespace App\Helpers\Attributes\Apply;

use App\Exceptions\HexbatchInvalidException;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\Attributes\AttributeValueType;
use App\Models\User;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use enshrined\svgSanitize\Sanitizer;

/**
 * Standard attributes are all final, except for the ones that are parents.
 * They are readable by anyone, unless made private in type
 * They have no bounds
 */
class StandardAttributes
{
    const DEF = [
        User::SYSTEM_NAME => [
            'name' => User::SYSTEM_NAME,
            'uuid' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'internal_description' => 'Base attribute for all standard attributes',
            'parent_uuid' => null,
            'value_type' => AttributeValueType::STRING,
            'validator' => null
        ],

        //standard info
        User::SYSTEM_NAME . '.info' => [
            'name' => User::SYSTEM_NAME . '.info',
            'uuid' => '016b2926-ab06-44af-b1c5-81520b39975b',
            'internal_description' => 'Attributes having to do with describing',
            'parent_uuid' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'value_type' => AttributeValueType::STRING,
            'validator' => null
        ],
        User::SYSTEM_NAME . '.info.name' => [
            'name' => User::SYSTEM_NAME . '.info.name',
            'uuid' => '09963b71-e8e9-41f3-b58f-95ea4c51fc38',
            'internal_description' => 'A name not restricted to the naming rules of the code',
            'parent_uuid' => '016b2926-ab06-44af-b1c5-81520b39975b',
            'value_type' => AttributeValueType::STRING,
            'validator' => null
        ],
        User::SYSTEM_NAME . '.info.email' => [
            'name' => User::SYSTEM_NAME . '.info.email',
            'uuid' => 'e3b2254c-3ebd-44c3-9cfb-406560a12880',
            'internal_description' => 'vaguely resembles email',
            'parent_uuid' => '016b2926-ab06-44af-b1c5-81520b39975b',
            'value_type' => AttributeValueType::STRING,
            'validator' => [StandardAttributes::class, 'validateEmail']
        ],
        User::SYSTEM_NAME . '.info.phone_number' => [
            'name' => User::SYSTEM_NAME . '.info.phone_number',
            'uuid' => '620b02d6-a9d6-4159-83a4-fd926688bcac',
            'internal_description' => 'e.164 only',
            'parent_uuid' => '016b2926-ab06-44af-b1c5-81520b39975b',
            'value_type' => AttributeValueType::STRING,
            'validator' => [StandardAttributes::class, 'validatePhone']
        ],
        User::SYSTEM_NAME . '.info.address' => [
            'name' => User::SYSTEM_NAME . '.info.address',
            'uuid' => '2b4b34f5-606f-47c0-8797-80f8ec38aed5',
            'internal_description' => 'any string',
            'parent_uuid' => '016b2926-ab06-44af-b1c5-81520b39975b',
            'value_type' => AttributeValueType::STRING,
            'validator' => null
        ],
        User::SYSTEM_NAME . '.info.map_location' => [
            'name' => User::SYSTEM_NAME . '.info.map_location',
            'uuid' => 'de7c7ae8-1d8a-4c4a-a0ec-ba80ca21c980',
            'internal_description' => '2d map location',
            'parent_uuid' => '016b2926-ab06-44af-b1c5-81520b39975b',
            'value_type' => AttributeValueType::MAP_BOUNDS,
            'validator' => [StandardAttributes::class, 'validateMapLocation']
        ],
        User::SYSTEM_NAME . '.info.timezone' => [
            'name' => User::SYSTEM_NAME . '.info.timezone',
            'uuid' => '5a188757-81a7-4dc5-87de-a08341a12c91',
            'internal_description' => 'name of the timezone',
            'parent_uuid' => '016b2926-ab06-44af-b1c5-81520b39975b',
            'value_type' => AttributeValueType::STRING,
            'validator' => [StandardAttributes::class, 'validateTimezone']
        ],
        User::SYSTEM_NAME . '.info.description' => [
            'name' => User::SYSTEM_NAME . '.info.description',
            'uuid' => '152c4fd4-d721-45c2-a75b-96d69992ef89',
            'internal_description' => 'any length text, or markdown or xml or json',
            'parent_uuid' => '016b2926-ab06-44af-b1c5-81520b39975b',
            'value_type' => AttributeValueType::STRING,
            'validator' => null
        ],



        //standard display
        User::SYSTEM_NAME . '.display' => [
            'name' => User::SYSTEM_NAME . '.display',
            'uuid' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'internal_description' => 'Attributes having to do with display',
            'parent_uuid' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'value_type' => AttributeValueType::STRING,
            'validator' => null
        ],
        User::SYSTEM_NAME . '.display.primary_color' => [
            'name' => User::SYSTEM_NAME . '.display.primary_color',
            'uuid' => 'c84278fc-a1be-4dc7-b47b-a24bab2464db',
            'internal_description' => 'Primary color',
            'parent_uuid' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
            'validator' => [StandardAttributes::class, 'validateColor']
        ],
        User::SYSTEM_NAME . '.display.secondary_color' => [
            'name' => User::SYSTEM_NAME . '.display.secondary_color',
            'uuid' => 'cfc6feb5-d92b-4db0-8146-058aee99a654',
            'internal_description' => 'Secondary color',
            'parent_uuid' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
            'validator' => [StandardAttributes::class, 'validateColor']
        ],
        User::SYSTEM_NAME . '.display.bg_color' => [
            'name' => User::SYSTEM_NAME . '.display.bg_color',
            'uuid' => '3b39ce42-a37f-45ef-9972-21b0592e0f87',
            'internal_description' => 'Background color',
            'parent_uuid' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
            'validator' => [StandardAttributes::class, 'validateColor']
        ],
        User::SYSTEM_NAME . '.display.opacity' => [
            'name' => User::SYSTEM_NAME . '.display.opacity',
            'uuid' => '8a569174-35a8-4af9-8531-1b07c135c63c',
            'internal_description' => 'Opacity',
            'parent_uuid' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::NUMERIC_NATURAL,
            'validator' => [StandardAttributes::class, 'validateOpacity']
        ],
        User::SYSTEM_NAME . '.display.svg' => [
            'name' => User::SYSTEM_NAME . '.display.svg',
            'uuid' => 'f5baab77-66bb-4ccc-917f-6f36afc70051',
            'internal_description' => 'svg',
            'parent_uuid' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING_XML,
            'validator' => [StandardAttributes::class, 'validateSvg']
        ],
        User::SYSTEM_NAME . '.display.image' => [
            'name' => User::SYSTEM_NAME . '.display.image',
            'uuid' => '2fa8ff2b-1735-4497-b7e0-83645e42240f',
            'internal_description' => 'url to image',
            'parent_uuid' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
            'validator' => [StandardAttributes::class, 'validateUrl']
        ],
        User::SYSTEM_NAME . '.display.small_thumbnail' => [
            'name' => User::SYSTEM_NAME . '.display.small_thumbnail',
            'uuid' => '43e9362d-2bf1-409e-9b87-cc29bf95560c',
            'internal_description' => 'url to image',
            'parent_uuid' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
            'validator' => [StandardAttributes::class, 'validateUrl']
        ],
        User::SYSTEM_NAME . '.display.medium_thumbnail' => [
            'name' => User::SYSTEM_NAME . '.display.medium_thumbnail',
            'uuid' => 'fbfff4ce-410f-42a0-9e99-87963b131446',
            'internal_description' => 'url to image',
            'parent_uuid' => 'd17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'value_type' => AttributeValueType::STRING,
            'validator' => [StandardAttributes::class, 'validateUrl']
        ],
        //category
        User::SYSTEM_NAME . '.category' => [
            'name' => User::SYSTEM_NAME . '.category',
            'uuid' => '09dc6180-f48a-4acb-9668-67e08a6d5ea3',
            'internal_description' => 'Base for all standard categories',
            'parent_uuid' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'value_type' => AttributeValueType::STRING,
            'validator' => null
        ],
        User::SYSTEM_NAME . '.category.remote' => [
            'name' => User::SYSTEM_NAME . '.category.remote',
            'uuid' => '618689d9-6dd2-40dc-a288-443d050c71bb',
            'internal_description' => 'Remote Category',
            'parent_uuid' => '09dc6180-f48a-4acb-9668-67e08a6d5ea3',
            'value_type' => AttributeValueType::STRING,
            'validator' => null
        ],
        User::SYSTEM_NAME . '.category.group' => [
            'name' => User::SYSTEM_NAME . '.category.group',
            'uuid' => 'f83aa255-862a-413f-8d9c-083d21c9d983',
            'internal_description' => 'User Group Category',
            'parent_uuid' => '09dc6180-f48a-4acb-9668-67e08a6d5ea3',
            'value_type' => AttributeValueType::STRING,
            'validator' => null
        ],
        User::SYSTEM_NAME . '.category.user' => [
            'name' => User::SYSTEM_NAME . '.category.user',
            'uuid' => '048a58ef-374c-4369-bc9a-fd04210d66a4',
            'internal_description' => 'User category',
            'parent_uuid' => '09dc6180-f48a-4acb-9668-67e08a6d5ea3',
            'value_type' => AttributeValueType::STRING,
            'validator' => null
        ],


        //admin roles
        User::SYSTEM_NAME . '.admin_role' => [
            'name' => User::SYSTEM_NAME . '.admin_role',
            'uuid' => '61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'internal_description' => 'Base for all standard admin roles',
            'parent_uuid' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'value_type' => AttributeValueType::NUMERIC_NATURAL,
            'validator' => null
        ],
        User::SYSTEM_NAME . '.admin_role.view_private_user_info' => [
            'name' => User::SYSTEM_NAME . '.admin_role.view_private_user_info',
            'uuid' => 'be806efe-fffb-4a39-a9ad-6b1b705c2fb1',
            'internal_description' => 'Users with this attribute can view all other user info',
            'parent_uuid' => '61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'value_type' => AttributeValueType::NUMERIC_NATURAL,
            'validator' => null
        ],
        User::SYSTEM_NAME . '.admin_role.set_sensitive_remote_types' => [
            'name' => User::SYSTEM_NAME . '.admin_role.set_sensitive_remote_types',
            'uuid' => 'a202179c-a8af-4f38-a612-7ddb719d4012',
            'internal_description' => 'Users with this attribute can set all the remote types',
            'parent_uuid' => '61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'value_type' => AttributeValueType::NUMERIC_NATURAL,
            'validator' => null
        ],
        User::SYSTEM_NAME . '.admin_role.view_all_remote_activity' => [
            'name' => User::SYSTEM_NAME . '.admin_role.view_all_remote_activity',
            'uuid' => '63b649da-2a3f-4940-8f78-ad8ac3109443',
            'internal_description' => 'Users with this attribute can see all remote activity by all users',
            'parent_uuid' => '61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'value_type' => AttributeValueType::NUMERIC_NATURAL,
            'validator' => null
        ],

        //set relationships
        User::SYSTEM_NAME . '.set_relation' => [
            'name' => User::SYSTEM_NAME . '.set_relation',
            'uuid' => '25197025-96e4-46dc-8b41-433a6336dc9d',
            'internal_description' => 'Base for all standard set relationships',
            'parent_uuid' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'value_type' => AttributeValueType::SET,
            'validator' => null
        ],

        //set types
        User::SYSTEM_NAME . '.set_type' => [
            'name' => User::SYSTEM_NAME . '.set_type',
            'uuid' => '1ea12c69-6fed-4fa7-a816-73326a222b82',
            'internal_description' => 'Base for all standard set types',
            'parent_uuid' => '6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'value_type' => AttributeValueType::STRING,
            'validator' => null
        ],


    ];

    public static function validateEmail($what): void
    {
        $b_ok = true;
        if (!is_string($what)) {
            $b_ok = false;
        }
        if ($b_ok) {
            //https://github.com/egulias/EmailValidator
            $validator = new EmailValidator();
            $b_ok = $validator->isValid("example@example.com", new RFCValidation()); //true
        }
        if (!$b_ok) {
            throw new HexbatchInvalidException(__("msg.not_email"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::EMAIL_ISSUE);
        }
    }

    public static function validatePhone($what): void
    {
        $b_ok = true;
        if (!is_string($what)) {
            $b_ok = false;
        }
        if ($b_ok) {
        //  e164'sh regex match only
            if (!preg_match('/^\+(?:[0-9]?){6,14}[0-9]$/', $what) ) {
                $b_ok = false;
            }
        }
        if (!$b_ok) {
            throw new HexbatchInvalidException(__("msg.not_e164_phone"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::MAP_COORDINATE_ISSUE);
        }
    }

    public static function validateMapLocation($what): void
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
                || ($maybe_coordination['longitude'] > 180 || $maybe_coordination['longitude'] < -180)
                || ($maybe_coordination['latitude'] > 90 || $maybe_coordination['latitude'] < -900)
            ) {
                $b_ok = false;
            }
        }

        if (!$b_ok) {
            throw new HexbatchInvalidException(__("msg.not_map_coordinate"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::MAP_COORDINATE_ISSUE);
        }
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


    public static function validateUrl($what): void
    {
        $b_ok = true;
        $maybe_coordination = Utilities::toArrayOrNull($what);
        if (!$maybe_coordination) {
            $b_ok = false;
        }
        if ($b_ok) {
            if (filter_var($what, FILTER_VALIDATE_URL) === FALSE) {
                $b_ok = false;
            }
        }
        if ($b_ok) {
            if (filter_var($what, FILTER_VALIDATE_IP,FILTER_FLAG_IPV4|FILTER_FLAG_IPV6|FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE)) {
                return;
            }
            if (filter_var($what, FILTER_VALIDATE_URL)) {
                return;
            } else {
                $b_ok = false;
            }
        }

        if (!$b_ok) {
            throw new HexbatchInvalidException(__("msg.not_url_or_ip"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::URL_ISSUE);
        }


    }

    public static function validateSvg($what): void
    {
        $b_ok = true;
        if (!is_string($what)) {
            $b_ok = false;
        }
        if ($b_ok) {
            try {
                $sanitizer = new Sanitizer();
                $cleanSVG = $sanitizer->sanitize($what);
                if (!$cleanSVG) {
                    $b_ok = false;
                }
            } catch (\Exception $e) {
                $b_ok = false;
            }
        }
        if (!$b_ok) {
            throw new HexbatchInvalidException(__("msg.not_svg"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::SVG_ISSUE);
        }
    }

    public static function validateOpacity($what): void
    {
        $b_ok = true;
        if (is_array($what) || is_object($what) || !intval($what)) {
            $b_ok = false;
        }

        $da_int = (int)$what;
        if ($b_ok && $da_int) {
            if($da_int < 0 || $da_int > 100) {
                $b_ok = false;
            }
        }
        if (!$b_ok) {
            throw new HexbatchInvalidException(__("msg.not_opacity"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::OPACITY_ISSUE);
        }
    }

    public static function validateColor($what): void
    {
        $b_ok = true;
        if (!is_string($what)) {
            $b_ok = false;
        }
        if ($b_ok) {
            try {
                \OzdemirBurak\Iris\Color\Factory::init($what);
            } catch (\Exception) {
                $b_ok = false;
            }
        }
        if (!$b_ok) {
            throw new HexbatchInvalidException(__("msg.not_color"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::COLOR_ISSUE);
        }


    }
}
