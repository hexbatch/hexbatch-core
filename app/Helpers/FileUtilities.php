<?php

namespace App\Helpers;

class FileUtilities
{
    /**
     * @author https://www.php.net/manual/de/function.copy.php comments section
     * @param string $src
     * @param string $dst
     * @return void
     */
    protected static function recurse_copy(string $src,string $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    static::recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    public static function recursiveCopy(string $src,string $dst)
    {
        if (is_dir($src)) {
            static::recurse_copy($src,$dst);
        } else {
            copy($src, $dst);
        }
    }

    /**
     * @author https://www.php.net/manual/en/function.rmdir.php#98622
     * @param string $dir
     * @return void
     */
    public static function recursiveDelete(string $dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir")
                        static::recursiveDelete($dir."/".$object);
                    else unlink   ($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function createTempDir(string $stub) : string  {
        $temp_file = tempnam(sys_get_temp_dir(),$stub);
        $tempdir = $temp_file . '_dir';
        mkdir($tempdir);
        unlink($temp_file);
        return $tempdir;

    }
}
