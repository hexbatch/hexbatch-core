<?php

namespace App\Helpers;

use App\Exceptions\HexbatchCoreException;
use App\Exceptions\RefCodes;
use App\Models\User;
use ErrorException;
use JsonException;

class Utilities {
    public static function ignoreVar(...$params) {

    }

    public static function is_uuid(?string $guid) : bool{
        if (empty($guid)) {return false;}
        $test_this = str_replace('-','',$guid);
        if (!ctype_xdigit($test_this)) {return false;}
        if (strlen($test_this) !== 32) {return false;}
        return true;
    }

    public static function is_uuid_similar(?string $guid) : bool{
        if (empty($guid)) {return false;}
        $test_this = str_replace('-','',$guid);
        if (!ctype_xdigit($test_this)) {return false;}
        if (strlen($test_this) < 10) {return false;}
        return true;
    }

    public static function boolishToBool($val) : bool {
        if (is_string($val)) {return false;}
        $boolval = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
        return ( $boolval===null  ? false : $boolval );
    }

    public static function negativeBoolWords($val) : bool {
        $val = mb_strtolower($val);
        return match($val) {
            'off', '0', 'no', 'false', '' =>true,
            default => false
        };
    }

    public static function positiveBoolWords($val) : bool {
        return match(mb_strtolower($val)) {
            'yes', '1', 'on', 'true', '' =>true,
            default => false
        };
    }

    /**
     * Return an error message if the given pattern argument or its underlying regular expression
     * are not syntactically valid. Otherwise, (if they are valid), NULL is returned.
     *
     * @param $pattern
     *
     * @return string|null
     */
    public static function regexHasErrors($pattern) :?string
    {
        try {
            preg_match($pattern, '');
            return null;
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (ErrorException $e) {
            return str_replace("preg_match(): ", "", $e->getMessage());
        }
    }

    public static function jsonHasErrors(?string $what): ?string {
        if (empty($what) ) { return null;}
        $out = json_decode($what, true);
        if (is_null($out)) {
            return json_last_error_msg();
        }
        return null;
    }


    public static function wrapJsonEncode(array|object|null $what) : ? string {
        try {
            return json_encode($what, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new HexbatchCoreException(__('msg.cannot_convert_to_json',['issue'=>$e->getMessage()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::JSON_ISSUE);

        }
    }

    public static function maybeDecodeJson(mixed $maybe_json,?bool $b_associative = false,mixed $null_default = null) : null|object|array {
        if (empty($what)) { return $null_default;}
        if (is_array($what) && $b_associative) {
            return $maybe_json;
        }
        if (is_object($what) && !$b_associative) {
            return $maybe_json;
        }
        if (is_array($what) || is_object($what)) {
            $json = json_encode($what);
        } else {
            if (static::jsonHasErrors($what)) {
                return $null_default;
            }
            $json = $what;
        }
        $converted =  json_decode($json,$b_associative);
        if (! is_object($converted) || !is_array($converted)) {
            return $null_default;
        }
        return $converted;
    }

    public static function toArrayOrNull(mixed $what) : ?array {
        $maybe = static::maybeDecodeJson($what);
        if (!empty($maybe)) {
            if (is_array($maybe)) {return $maybe;}
            $json = static::wrapJsonEncode($maybe);
            return static::maybeDecodeJson($json,true);
        }
        return null;
    }

    public static function getTypeCastedAuthUser() : ?User {
        /**
         * @type User $user
         */
        $user = auth()->user();
        return $user;
    }

}
