<?php

namespace App\Helpers;

class Utilities {
    public static function is_uuid(?string $guid) : bool{
        if (empty($guid)) {return false;}
        $test_this = str_replace('-','',$guid);
        if (!ctype_xdigit($test_this)) {return false;}
        if (strlen($test_this) !== 32) {return false;}
        return true;
    }
}
