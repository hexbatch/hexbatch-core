<?php

namespace App\Helpers;

use App\Enums\Rules\TypeOfMergeLogic;
use App\Models\ThingDatum;

class ArrayMerge
{
    public static function mergeArrays(TypeOfMergeLogic $logic,array $array_of_arrays) : array {
         $ret = [];
         foreach ($array_of_arrays as $some_array)
         {
             switch ($logic) {
                 case TypeOfMergeLogic::INTERSECTION : {
                     $ret = static::mergeInterection($ret,$some_array);
                     break;
                 }
                 default: throw new \LogicException("Not implemented merge");
             }
         }

         return $ret;
    }

    /**
     * @param array|ThingDatum[] $a
     * @param array|ThingDatum[]  $b
     * @return array|ThingDatum[]
     */
    protected static function mergeInterection(array $a, array $b) : array {
        $ret = [];
        //todo fill out this intersection function and the other ones too
        //if regular array merge top keys, or top arrays only if one is not in the other

        //if thing data merge by what it points to (use ids in each position)
        return $ret;
    }
}
