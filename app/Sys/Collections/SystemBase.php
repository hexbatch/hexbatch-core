<?php

namespace App\Sys\Collections;

use App\Helpers\Utilities;
use App\Sys\Build\SystemResources;
use App\Sys\Res\ISystemModel;
use App\Sys\Res\ISystemResource;

use Hexbatch\Thangs\HexbatchThangsProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

abstract class SystemBase
{
    const SOURCE_FOLDER = '';

    /** @var ISystemResource[] $class_name_array */
    public static array $class_name_array = []; //sorted by nested folder: higher level php classes before those in folders below them

    /** @var array<string,SystemBase> $uuid_class_names */
    protected static array $uuid_class_names = []; //keyed by UUID , value is the namespaced class name found in the values above


    /** @var array<string,ISystemResource> $resource_array */
    protected static array $resource_array = []; //keyed by uuid
    /**
     * @return string[]
     */
    public static function getUuids() { return array_keys(static::$uuid_class_names);}


    protected static function callClass($some_class_name) : ?ISystemResource {
        $ret = null;

        if (class_exists($some_class_name))
        {
            $uuid_check = static::getUuid($some_class_name);
            if (isset(static::$resource_array[$uuid_check])) {
                return static::$resource_array[$uuid_check];
            }
            /** @var ISystemResource $obj */
            $obj = static::makeNewClass($some_class_name);
            $ret = $obj->OnCall();
            $uuid = static::getUuid($some_class_name);
            static::$resource_array[$uuid] = $ret;
        }
        return $ret;
    }

    protected static function makeNewClass(string $some_class_name) : ISystemResource {


        /** @type ISystemResource */
        return new $some_class_name(b_type_init:true);
    }


    public static function loadClasses() : array  {
        if (empty(static::$class_name_array)) { static::$class_name_array = static::findClasses(); static::postFindClasses();}
        return static::$class_name_array;
    }

    /**
     * @return ISystemResource[]
     */
    public static function generateObjects() : array
    {
        static::loadClasses();
        $ret = [];
        foreach (static::$class_name_array as $some_class_name) {
            $what = static::callClass($some_class_name);
            if ($what?->didCreateModel()) {
                $ret[] = $what;
            }
        }
        return $ret;
    }


    public static function doNextStepC() {

    }
    public static function doNextStepB() {

    }
    public static function doNextStep() {

        foreach (static::$resource_array as $res) {
            $res->onNextStep();
        }
    }

    public static function getUuid(null|string|ISystemResource  $class_name_or_uuid) :string {
        $uuid = null;
        if (class_exists($class_name_or_uuid)) {
            $interfaces = class_implements($class_name_or_uuid);
            if (isset($interfaces['App\Sys\Res\ISystemResource'])) {
                $uuid = $class_name_or_uuid::getClassUuid();
            }
        }
        else if (is_string($class_name_or_uuid) && Utilities::is_uuid($class_name_or_uuid)) {
            $uuid = $class_name_or_uuid;
        }
        return $uuid;
    }
    public static function getResourceByUuid(null|string|ISystemResource  $class_name_or_uuid) : ?ISystemResource {
        if (empty($class_name_or_uuid)) {return null;}
        static::loadClasses();
        $uuid = static::getUuid($class_name_or_uuid);
        if (empty($uuid)) {return null;}

        $class_name = static::$uuid_class_names[$uuid]??null;
        if (!$class_name) {return null;}
        if (isset(static::$resource_array[$uuid])) {return static::$resource_array[$uuid];}
        return static::callClass($class_name);
    }

    protected  static function postFindClasses() : void {

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
                $interfaces = class_implements($full_class_name);

                if (isset($interfaces['App\Sys\Res\ISystemResource'])) {

                    $classes[$full_class_name] = explode('\\', $full_class_name);
                    /**
                     * @type ISystemResource $full_class_name
                     */
                    if ($full_class_name::getClassUuid()) {
                        static::$uuid_class_names[$full_class_name::getClassUuid()] = $full_class_name;
                        SystemResources::addToUuidDictionary($full_class_name::getClassUuid(),$full_class_name);
                    }
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

    public static function extract_namespace($file) {
        return HexbatchThangsProvider::extract_namespace($file);
    }

    /**
     * returns array of uuid that has not been added to the db yet
     * @return ISystemResource[]
     */
    public static function getNew() :array {
        $all_uuids = static::getUuids();
        $current_uuids = static::getCurrentUuids();
        $diff_uuids =  array_diff($all_uuids,$current_uuids);
        $ret = [];
        foreach ($diff_uuids as $some_uuid) {
            $class_name = static::$uuid_class_names[$some_uuid]??null;
            if ($class_name) {
                $ret[] = $class_name;
            }
        }
        return $ret;
    }

    /**
     * returns array of uuid
     * @return string[]
     */
    public static function getCurrentUuids() :array {
        $models_now = static::getCurrentModels();
        $ret = [];

        foreach ($models_now as $mod) {
            $ret[] = $mod->ref_uuid;
        }
        return $ret;
    }



    /**
     * returns array of deleted models
     * @return ISystemModel[]
     */
    public static function removeOld() :array {
        $olds = static::getOldModels();
        foreach ($olds as $old) {
            $old->delete();
        }
        return $olds;
    }


    /**
     * returns array of models that are current between resources and db
     * * @return ISystemModel[]
     */
    abstract public static function getCurrentModels() :array ;

    /**
     * returns array of models that no longer fit with the resources
     * @return ISystemModel[]
     */
    abstract public static function getOldModels() :array ;


}

