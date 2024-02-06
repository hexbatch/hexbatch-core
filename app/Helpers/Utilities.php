<?php

namespace App\Helpers;

use ErrorException;
use JsonException;

class Utilities {
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

    public static function boolishToBool($val) {
        $boolval = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
        return ( $boolval===null  ? false : $boolval );
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

    public static function convertToObject(array|string|object $what) : null|object {
        if (empty($what)) { return null;}
        if (is_array($what) || is_object($what)) {
            $json = json_encode($what);
        } else {
            if (static::jsonHasErrors($what)) {
                return null;
            }
            $json = $what;
        }
        $converted =  json_decode($json,false);
        if (! is_object($converted)) {
            return null;
        }
        return $converted;
    }

}
