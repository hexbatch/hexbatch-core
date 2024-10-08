<?php

namespace App\Helpers;

use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\User;
use App\Models\UserNamespace;
use ErrorException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use JsonException;
use JsonPath\InvalidJsonPathException;
use JsonPath\JsonPath;
use LogicException;

class Utilities {
    public static function ignoreVar(...$params) {}

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
        if (empty($val)) {return false;}
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

    public static function cleanMaybeIntArrayToUniqueAndSorted(array $vals) : array {
        $ret = [];
        foreach ($vals as $val) {
            if (is_array($val) || is_object($val)) {
                throw new LogicException("This is not the int you were looking for");
            }
            $ret[] = intval($val);

        }
        $ret = array_unique($ret,SORT_NUMERIC);
        usort($ret,function(int $a,int $b) {
            return $a <=> $b;
        });
        return $ret;
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

    public static function testValidJsonPath(?string $maybe_json_path): void {
        if (empty($maybe_json_path)) {return;}
        try {
            $test = [1,2,3,"apples"=>"two"];//this is just to test, and is ok to keep, this can be any array
            JsonPath::get($test,$maybe_json_path);
        } /** @noinspection PhpRedundantCatchClauseInspection */
        catch (InvalidJsonPathException) {
            throw new HexbatchNotPossibleException(__("msg.invalid_json_path",['ref'=>$maybe_json_path]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::JSON_PATH_ISSUE);

        }
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

    public static function maybeEncodeJson(mixed $what) : ?string  {
        if (empty($what)) {return $what;}
        if (is_array($what) || is_object($what)) {
            return static::wrapJsonEncode($what);
        }
        return strval($what);
    }

    public static function maybeDecodeJson(mixed $maybe_json,?bool $b_associative = false,mixed $null_default = null) : null|object|array|string {
        if (empty($maybe_json)) { return $null_default;}
        if (is_array($maybe_json) && $b_associative) {
            return $maybe_json;
        }
        if (is_object($maybe_json) && !$b_associative) {
            return $maybe_json;
        }
        if (is_array($maybe_json) || is_object($maybe_json)) {
            $json = json_encode($maybe_json);
        } else {
            if (static::jsonHasErrors($maybe_json)) {
                return $maybe_json;
            }
            $json = $maybe_json;
        }
        $converted =  json_decode($json,$b_associative);
        if (! (is_object($converted) || is_array($converted) )) {
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

    public static function getDefaultNamespace() : ?UserNamespace {

        $user = static::getTypeCastedAuthUser();
        return $user->default_namespace;
    }

    public static function getCurrentNamespace() : ?UserNamespace {
        //todo use the system namespace is the param is not set
        $namespace = null;
        $what_route = Route::current();
        if ($what_route->hasParameter('user_namespace')) {
            $namespace = $what_route->parameter('user_namespace');
            if (!$namespace) {
                $user_namespace_name = $what_route->originalParameter('user_namespace');
                if ($user_namespace_name) {
                    $namespace = (new UserNamespace())->resolveRouteBinding($user_namespace_name);
                }

            }
            if (!$namespace instanceof UserNamespace) {
                throw new LogicException("getCurrentNamespace does not see a Namespace in the parameter");
            }
        }
        return $namespace;
    }

    public static function runDbFile(?string $start_path) :bool {
        $path = realpath($start_path);
        if (!$path) {
            throw new LogicException("could not find file in migration: $start_path");
        }
        $proc = file_get_contents($path);
        if (!$proc) {
            throw new LogicException("could not read file in migration: $path");
        }
        return DB::statement($proc);
    }

    const BASE_64_OPTION = 'base64';
    /**
     * return a base64 encrypted string, you can also choose hex or null as encoding.
     * @source https://stackoverflow.com/a/62175263/2420206
     * @example $enc = str_encrypt_aes_256_gcm("my-secretText", "myPassword", "base64");
     */
    public static function str_encrypt_aes_256_gcm(?string $plaintext, string $password, ?string $encoding = self::BASE_64_OPTION) : ?string {
        if (empty($plaintext) ) {
            return null;
        }
        if (empty($password)) {
            throw new LogicException("str_encrypt_aes_256_gcm needs password to not be empty");
        }
        $keysalt = openssl_random_pseudo_bytes(16);
        $key = hash_pbkdf2("sha512", $password, $keysalt, 20000, 32, true);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-gcm"));
        $tag = "";
        $encryptedstring = openssl_encrypt($plaintext, "aes-256-gcm", $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($encryptedstring === false) {
            throw new \RuntimeException("Cannot str_encrypt_aes_256_gcm ");
        }
        return $encoding === "hex" ? bin2hex($keysalt.$iv.$encryptedstring.$tag) :
            ($encoding === self::BASE_64_OPTION ? base64_encode($keysalt.$iv.$encryptedstring.$tag) : $keysalt.$iv.$encryptedstring.$tag);
    }

    /**
     * decrypt something made in str_encrypt_aes_256_gcm
     * @source https://stackoverflow.com/a/62175263/2420206
     * @example $dec = str_decrypt_aes_256_gcm($enc, "myPassword", "base64");
     */
    public static function str_decrypt_aes_256_gcm(?string $encrypted_string, string $password, ?string $encoding = self::BASE_64_OPTION) : ?string  {

        if (empty($encrypted_string) ) {
            return null;
        }

        if (empty($password)) {
            throw new LogicException("str_decrypt_aes_256_gcm needs args to not be empty");
        }

        $encrypted_string = $encoding === "hex" ? hex2bin($encrypted_string) : ($encoding === self::BASE_64_OPTION ? base64_decode($encrypted_string) : $encrypted_string);
        $keysalt = substr($encrypted_string, 0, 16);
        $key = hash_pbkdf2("sha512", $password, $keysalt, 20000, 32, true);
        $ivlength = openssl_cipher_iv_length("aes-256-gcm");
        $iv = substr($encrypted_string, 16, $ivlength);
        $tag = substr($encrypted_string, -16);
        $work_or_false_on_fail =  openssl_decrypt(substr($encrypted_string, 16 + $ivlength, -16), "aes-256-gcm", $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($work_or_false_on_fail === false) {
            throw new \RuntimeException("Cannot str_decrypt_aes_256_gcm ");
        }
        return $work_or_false_on_fail;
    }

    public static function strip_tags_convert_entities(?string $what) : ?string  {
        if (empty($what)) {return $what; }
        return strip_tags(htmlspecialchars($what,ENT_QUOTES| ENT_HTML401,'UTF-8',false));
    }

    public  static function cleanAnsiFromString(string $what) : string {
        $step_a = preg_replace(/** @lang text */ '#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $what);
        $step_b = preg_replace('#\\.[.]+#','-->',$step_a,1);
        $step_c = preg_replace('#\\.[.]+#','',$step_b);
        $step_d = htmlspecialchars_decode($step_c);
        $step_e = str_replace('&gt;','>',$step_d);
        return $step_e;
    }

    public static function getVersionString() : ?string {
        return file_get_contents(base_path() . DIRECTORY_SEPARATOR . 'version') ? : null;
    }

    /**
     * To compare a version string as a number
     * @author https://stackoverflow.com/questions/1142496/regex-replace-for-decimals (the heart of it,The regex and the if statement)
     * @example converts something like '0.1.2' to 0.12
     * @param string|null $version_string
     *
     * @return float|null
     */
    public static function convertVersionToFloat(?string $version_string) : ?float {
        if (empty($version_string)) {return null;}
        $dashes_and_underscores_to_points = str_replace('-','.',$version_string);
        $dashes_and_underscores_to_points = str_replace('_','.',$dashes_and_underscores_to_points);
        $only_numbers_and_points = preg_replace('/[^\d.]+/', '', $dashes_and_underscores_to_points);
        if (($pos = strpos($only_numbers_and_points, '.')) !== false) {
            $no_extra_points = substr($only_numbers_and_points, 0, $pos+1).str_replace('.', '', substr($only_numbers_and_points, $pos+1));
        } else {
            $no_extra_points = $only_numbers_and_points;
        }
        $val = floatval($no_extra_points);
        return $val;
    }

    /** @noinspection PhpUnused */
    public static function getVersionFloat() : float {

        $string_version = static::getVersionString();
        return static::convertVersionToFloat($string_version);
    }

}
