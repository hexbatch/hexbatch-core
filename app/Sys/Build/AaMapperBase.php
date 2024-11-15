<?php

namespace App\Sys\Build;


class AaMapperBase
{
    /** @noinspection PhpUnused */
    const SOURCE_FOLDER = '';
    const OUTPUT_FILE = '';


    public static function getOutputPath() : string {
        return base_path(static::OUTPUT_FILE);
    }
    public static function writeToStandardFile() {
        static::writeToFile(static::getOutputPath());
    }
    public static function writeToFile(string $file_path) : void {
        $map_data = static::getMapData();
        $pre = '<?php return ';
        if (empty($map_data)) {
            $b_result = file_put_contents($file_path,$pre. ' [];');
            if ($b_result === false) {
                throw new \LogicException("Could not write (C) to $file_path");
            }
            return;
        }
        $out = [];
        foreach ( $map_data as $entry) {
            if (!$entry->isDataComplete()) {continue;}
            $out[$entry->getUuid()] = $entry->toArray() ;
        }

        $all = $pre . json_encode($out,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK| JSON_PRETTY_PRINT) . "\n;";
        $all= str_replace(":","=>",$all);
        $all= str_replace("}","]",$all);
        $all= str_replace("{","[",$all);

        $b_result = file_put_contents($file_path,$all);
        if ($b_result === false) {
            throw new \LogicException("Could not write to $file_path");
        }
    }


    /**
     * @return ActionMapEntry[]|ApiMapEntry[]
     */
    public static function getMapData() : array {return [];}
}
