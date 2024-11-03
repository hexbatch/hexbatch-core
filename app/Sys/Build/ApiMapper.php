<?php

namespace App\Sys\Build;

use App\Sys\Collections\SystemBase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * strategy:
 * get hash of api name with subarray for the different interfaces and the api type
 * write hash to file keyed by uuid from the api type, put api name in the structure
 */
class ApiMapper extends AaMapperBase
{
    const SOURCE_FOLDER = 'app/Api/Thinger';
    const OUTPUT_FILE = 'bootstrap/cache/hbc_api_cache.php';


    public static function getApiInterface(BuildApiFacet $facet,string $uuid) :?string {
        $what = include base_path(static::OUTPUT_FILE);
        if (isset($what[$uuid]) ) {
            return $what[$uuid][$facet->value]??null;
        }
        return null;
    }

    /**
     * @return ApiMapEntry[]
     */
    public static function getMapData() : array
    {

        $directory = new RecursiveDirectoryIterator(base_path(static::SOURCE_FOLDER));
        $flattened = new RecursiveIteratorIterator($directory);

        $files = new RegexIterator($flattened, '#\.(?:php)$#Di');
        $map_entries = [];
        foreach($files as $file) {
            $namespace = SystemBase::extract_namespace($file);
            $class = basename($file, '.php');
            $full_class_name = $namespace . '\\' .$class;
            if (is_subclass_of($full_class_name, 'App\Sys\Res\Types\Stk\Root\Api') ) {
                $uuid = $full_class_name::getClassUuid();
                if (empty($map_entries[$uuid])) {
                    $map_entries[$uuid] = new ApiMapEntry();
                }
                $map_entries[$uuid]->setApi($full_class_name);
            }

        }



        usort($map_entries,
            function(ApiMapEntry $a, ApiMapEntry $b) {
                return $a->getApiName() <=> $b->getApiName();
            });


        return $map_entries;

    }
}
