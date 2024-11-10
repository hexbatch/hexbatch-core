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
class ActionMapper extends AaMapperBase
{
    const SOURCE_FOLDER = 'app/Api/Cmd';
    const OUTPUT_FILE = 'bootstrap/cache/hbc_action_cache.php';


    public static function getActionInterface(BuildActionFacet $facet,string $uuid) {
        $what = include base_path(static::OUTPUT_FILE);

        if (!isset($what[$uuid]) || !isset($what[$uuid][$facet->value]) ) {
           throw new \LogicException("The facet $facet->value for $uuid is not in the bootstrap file ".static::OUTPUT_FILE);
        }
        $class = $what[$uuid][$facet->value];
        return new $class;
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
                $map_entries[$uuid]->setAction($full_class_name);
            }

        }



        usort($map_entries,
            function(ActionMapEntry $a, ActionMapEntry $b) {
                return $a->getActionName() <=> $b->getActionName();
            });


        return $map_entries;

    }
}
