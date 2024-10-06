<?php

namespace App\System;

abstract class SystemBase
{
    const SOURCE_FOLDER = '';
    public static function generateObjects() : array
    {
        $ret = [];
        $absolute_path = base_path(static::SOURCE_FOLDER);
        foreach (glob($absolute_path.'/*.php') as $file)
        {
            require_once $file; //not needed with composer

            // get the file name of the current file without the extension
            // which is essentially the class name
            $class = basename($file, '.php');

            if (class_exists($class))
            {
                $obj = new $class;
                $ret[] = $obj->OnCall();
            }
        }
        return $ret;
    }
}

//https://stackoverflow.com/questions/21559957/create-instances-of-all-classes-in-a-directory-with-php
/*

 */
