<?php

namespace App\System;

use App\System\Resources\ISystemResource;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

abstract class SystemBase
{
    const SOURCE_FOLDER = '';
    public static array $class_name_array; //sorted by nested folder: higher level php classes before those in folders below them
    protected static array $uuid_class_names = []; //keyed by UUID , value is the namespaced class name found in the values above

    /**
     * @var ISystemResource[] $resource_array
     */
    protected static array $resource_array = []; //keyed by uuid

    protected static function callClass($some_class_name) : ?ISystemResource {
        $ret = null;
        if (class_exists($some_class_name))
        {
            $obj = new $some_class_name;
            $ret = $obj->OnCall();
            if (defined($some_class_name::UUID)) {
                static::$resource_array[$some_class_name::UUID] = $ret;
            }
        }
        return $ret;
    }


    public static function generateObjects() : array
    {
        if (empty(static::$class_name_array)) {static::$class_name_array = static::findClasses();}
        static::$resource_array = [];
        $ret = [];
        foreach (static::$class_name_array as $some_class_name) {
            $ret[] = static::callClass($some_class_name);

        }
        return $ret;
    }


    public static function doNextStep() {
        foreach (static::$resource_array as $res) {
            $res->onNextStep();
        }
    }

    public static function getResourceByUuid(string $uuid) : ?ISystemResource {
        if (empty(static::$class_name_array)) {static::$class_name_array = static::findClasses();}
        $class_name = static::$uuid_class_names[$uuid]??null;
        if (!$class_name) {return null;}
        if (isset(static::$resource_array[$uuid])) {return static::$resource_array[$uuid];}
        return static::callClass($class_name);
    }

    public static function updateResourceGuid(string $old_guid,string $new_guid) {
        static::$uuid_class_names[$new_guid] = static::$uuid_class_names[$old_guid];
        static::$resource_array[$new_guid] = static::$resource_array[$old_guid];
    }



    public static function findClasses() : array
    {

        $directory = new RecursiveDirectoryIterator(base_path(static::SOURCE_FOLDER));
        $flattened = new RecursiveIteratorIterator($directory);

        $files = new RegexIterator($flattened, '#\.(?:php)$#Di');
        $classes = [];
        foreach($files as $file) {
            $namespace = static::extract_namespace($file);
            $class = basename($file, '.php');
            $full_class_name = $namespace . '\\' .$class;
            if (class_exists($full_class_name))
            {
                $classes[$full_class_name] =  explode('\\',$full_class_name);
                if (defined($full_class_name::UUID)) {
                    static::$uuid_class_names[$full_class_name::UUID] = $full_class_name;
                }
            }

        }

        usort($classes, function($a, $b) {
            for($i=0; $i < count($a) && $i < count($b); $i++) {
                $comp = $a <=> $b;
                if ($comp) {return $comp;}
            }
            return count($a) <=> count($b);
        });

        $class_name_array = [];
        foreach ($classes as $some_class_array) {
            $some_class = implode('\\',$some_class_array);
            $class_name_array[] = $some_class;

        }
        return $class_name_array;

    }

    protected static function extract_namespace($file) {
        $ns = NULL;
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (str_starts_with($line, 'namespace')) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }
        return $ns;
    }
}

