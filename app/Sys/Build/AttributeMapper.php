<?php

namespace App\Sys\Build;

use App\Sys\Collections\SystemBase;
use App\Sys\Res\Atr\BaseAttribute;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * strategy:
 * get hash of api name with subarray for the different interfaces and the api type
 * write hash to file keyed by uuid from the api type, put api name in the structure
 */
class AttributeMapper extends AaMapperBase
{
    const SOURCE_FOLDER = 'app/Sys/Res/Atr/Stk';
    const OUTPUT_FILE = 'bootstrap/cache/hbc_attribute_cache.php';


    public static function getAttributeEntry(string $uuid) :AttributeMapEntry
    {
        $what = include base_path(static::OUTPUT_FILE);
        if (!isset($what[$uuid]) || !isset($what[$uuid]['class']) ) {
            throw new \LogicException("getAttributeEntry: Cannot find api for $uuid. It is not in the ".static::OUTPUT_FILE);
        }

        return new AttributeMapEntry(info:$what[$uuid]);
    }


    public static function getAttributeClass(string $uuid) :string|BaseAttribute
    {
        $what = static::getAttributeEntry($uuid);
        /** @type BaseAttribute */
        return  $what->getFullClassName();
    }

    /**
     * @return ActionMapEntry[]
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
            if (is_subclass_of($full_class_name, 'App\Sys\Res\Atr\BaseAttribute') ) {
                $uuid = $full_class_name::getClassUuid();
                if (empty($map_entries[$uuid])) {
                    $map_entries[$uuid] = new AttributeMapEntry();
                }
                $map_entries[$uuid]->setFromClassName($full_class_name);
            }

        }



        usort($map_entries,
            function(AttributeMapEntry $a, AttributeMapEntry $b) {
                return $a->getInternalName() <=> $b->getInternalName();
            });


        return $map_entries;

    }
}
