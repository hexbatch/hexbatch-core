<?php

namespace App\Sys\Build\Mappers;

use App\Sys\Collections\SystemBase;
use App\Sys\Res\Types\Stk\Root\Api;
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
    const SOURCE_FOLDER = 'app/Sys/Res/Types/Stk/Root/Api';
    const OUTPUT_FILE = 'bootstrap/cache/hbc_api_cache.php';



    public static function getApiEntry(string $uuid) :ApiMapEntry
    {
        $what = include base_path(static::OUTPUT_FILE);
        if (!isset($what[$uuid]) || !isset($what[$uuid]['class']) ) {
            throw new \LogicException("getApiEntry: Cannot find api for $uuid. It is not in the ".static::OUTPUT_FILE);
        }

        return new ApiMapEntry(info:$what[$uuid]);
    }

    /**
     * @param string $uuid
     * @return string|Api
     */
    public static function getApiClass(string $uuid) :string|Api
    {
        $what = static::getApiEntry($uuid);
        /** @type Api */
        return  $what->getFullClassName();
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
                $map_entries[$uuid]->setFromClassName($full_class_name);
            }

        }



        usort($map_entries,
            function(ApiMapEntry $a, ApiMapEntry $b) {
                return $a->getInternalName() <=> $b->getInternalName();
            });


        return $map_entries;

    }
}
