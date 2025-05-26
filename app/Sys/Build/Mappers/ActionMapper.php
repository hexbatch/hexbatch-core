<?php

namespace App\Sys\Build\Mappers;

use App\Sys\Collections\SystemBase;
use App\Sys\Res\Types\Stk\Root\Act\BaseAction;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * strategy:
 * get hash of api name with subarray for the different interfaces and the api type
 * write hash to file keyed by uuid from the api type, put api name in the structure
 */
class ActionMapper extends AaMapperBase
{
    const SOURCE_FOLDER = 'app/Sys/Res/Types/Stk/Root/Act/Cmd';
    const OUTPUT_FILE = 'bootstrap/cache/hbc_action_cache.php';


    public static function getActionEntry(string $uuid) :ActionMapEntry
    {
        $what = include base_path(static::OUTPUT_FILE);
        if (!isset($what[$uuid]) || !isset($what[$uuid]['class']) ) {
            throw new \LogicException("getActionEntry: Cannot find api for $uuid. It is not in the ".static::OUTPUT_FILE);
        }

        return new ActionMapEntry(info:$what[$uuid]);
    }


    public static function getActionClass(string $uuid) :string|BaseAction
    {
        $what = static::getActionEntry($uuid);
        /** @type BaseAction */
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
            if (is_subclass_of($full_class_name, 'App\Sys\Res\Types\Stk\Root\Act\BaseAction') ) {
                $uuid = $full_class_name::getClassUuid();
                if (empty($map_entries[$uuid])) {
                    $map_entries[$uuid] = new ActionMapEntry();
                }
                $map_entries[$uuid]->setFromClassName($full_class_name);
            }

        }



        usort($map_entries,
            function(ActionMapEntry $a, ActionMapEntry $b) {
                return $a->getInternalName() <=> $b->getInternalName();
            });


        return $map_entries;

    }
}
