<?php /** @noinspection DuplicatedCode */
namespace App\Enums\Rules;
use App\Models\ActionDatum;

/**
 * postgres enum type_of_merge_logic
 */
enum TypeOfMergeLogic : string {




    /**
     * intersection:
     *  json if object only common keys kept, if array only elements that are same in all arrays kept
     *  data: only the common parts shared in all children are kept (if all children do not have that data its discarded)
     */
    case INTERSECTION = 'intersection'; //intersection

    /**
        difference:
            json if object only uncommon keys kept, if array only dissimilar array elements kept
            data only different data kept (so all same data shared by two or more children discarded)
     */
    case DIFFERENCE = 'difference';


    /**
     * union:
     *  json if object combine the keys, the higher rank's value is kept,  if array then all elements merged but are unique in array
     *  data: combine all child data
     */
    case UNION = 'union'; //union, keys combined, arrays combined


    public static function tryFromInput(string|int|bool|null $test ) : TypeOfMergeLogic {
        $maybe  = TypeOfMergeLogic::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfMergeLogic::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }


    public static function mergeArrays(TypeOfMergeLogic $logic,array $array_of_arrays) : array {
        $ret = [];
        foreach ($array_of_arrays as $some_array)
        {
            switch ($logic) {
                case TypeOfMergeLogic::UNION : {
                    $ret = TypeOfMergeLogic::mergeUnion($ret,$some_array);
                    break;
                }
                case TypeOfMergeLogic::INTERSECTION : {
                    if (empty($ret)) {$ret = $some_array;} else {$ret = TypeOfMergeLogic::mergeIntersection($ret,$some_array);}
                    break;
                }
                case TypeOfMergeLogic::DIFFERENCE : {
                    $ret = TypeOfMergeLogic::mergeDifference($ret,$some_array);
                    break;
                }
            }
        }


        return $ret;
    }




    /**
     *  each array can have elements, each element can be a key or list: with primitives, arrays (list or hash) or ActionDatum
     *  The first array has the items all the other arrays must have
     *  the second array can have matching items anywhere if both are a list, but if hash the keys must match
     *
     *  so, we start to marge from left to right
     *  if both arrays are hash, then combine the hashes
     *  if both arrays are list, then combine the elements, the elements are unique in the returned list or a nested list inside
     * if one is list and the other is key, then return no common elements

     */
    protected static function mergeIntersection( array $a, array  $b) : array {


        if (array_is_list($a) && !array_is_list($b)) {return [];}
        if (array_is_list($b) && !array_is_list($a)) {return [];}
        $ret = [];
        if (array_is_list($b) && array_is_list($a)) {
            //primitives and ActionDatum can be in different indexes, and repeated, but we will combine any repeats. Arrays are index dependent, same index arrays are intersected
            $rem = [];

            //build hash of ActionDatum
            $b_data = [];
            foreach ($b as  $b_val) {
                if ($b_val instanceof ActionDatum) {
                    $b_data[strval($b_val->id)] = $b_val;
                }
            }


            foreach ($a as $a_index => $a_val) {

                $a_type = gettype($a_val);
                if ($a_type==='boolean' || $a_type==='integer' || $a_type==='double' || $a_type==='string') {
                    if (in_array($a_val,$b,true) && !in_array($a_type,$rem,true)) {
                        $rem[] = $a_val;
                        $ret[] =$a_val;
                    }
                }
                elseif ($a_val instanceof ActionDatum) {
                    if (array_key_exists(strval($a_val->id),$b_data)) {
                        unset($b_data[$a_val->id]); //keep data unique
                        $ret[] = $a_val;
                    }
                }
                elseif ($a_type === 'array') {
                    if (is_array($b[$a_index]??null)) {
                        $maybe = TypeOfMergeLogic::mergeIntersection($a_val,$b[$a_index]);
                        if (!empty($maybe)) { $ret[] = $maybe;}
                    }
                }


            }
            //see if any remaining array is duplicated, if so remove that
            $rem_arrays = [];
            foreach ($ret as $r_index => $r_val) {
                if (!is_array($r_val)) {continue;}
                $json = json_encode($r_val);
                if (in_array($json,$rem_arrays)) { unset($r_val[$r_index]);}
                $rem_arrays[] = $json;
            }
        } else {

            //do the hashes get common keys and compare the value of the key, the value can be a primitive, array or ActionDatum
            $distict_keys = array_intersect_key($a,$b);
            foreach ($distict_keys as $some_key) {
                $a_val = $a[$some_key];
                $b_val = $b[$some_key];
                $a_type = gettype($a_val);
                if ($a_type==='boolean' || $a_type==='integer' || $a_type==='double' || $a_type==='string') {
                    if ($a_val === $b_val) { $ret[$some_key]= $a_val;}
                } else if ($a_val instanceof ActionDatum && $b_val instanceof ActionDatum) {
                    if ($a_val->id === $b_val->id) { $ret[$some_key] = $a_val; }
                } else if( is_array($a_val) && is_array($b_val)) {
                    $maybe = TypeOfMergeLogic::mergeIntersection($a_val,$b_val);
                    if (!empty($maybe)) { $ret[$some_key] = $maybe;}
                }
            }
        }
        return $ret;
    }





    /**
     * each array can have elements, each element can be a key or list:  with primitives, arrays (list or hash) or ActionDatum
     * the results is items not in either array
     * if one array is a list and the other is a hash, then return empty array
     * if both are lists, then only unique items, any arrays kept have their original structure
     * if both are hashes, then only keys that are different
     * @param array|ActionDatum[] $a
     * @param array|ActionDatum[]  $b
     * @return array|ActionDatum[]
     */
    protected static function mergeDifference(array $a, array $b) : array {

        if (array_is_list($a) && !array_is_list($b)) {return [];}
        if (array_is_list($b) && !array_is_list($a)) {return [];}
        $ret = [];
        if (array_is_list($b) && array_is_list($a)) {
            //go through $a, if not in $b, then include in the return, if array use json so build up map first
            $rem_b_arrays = [];
            foreach ($b as $b_val) {
                if (!is_array($b_val)) {continue;}
                $json = json_encode($b_val);
                $rem_b_arrays[] = $json;
            }

            $rem_a_arrays = [];
            foreach ($a as $a_val) {
                if (!is_array($a_val)) {continue;}
                $json = json_encode($a_val);
                $rem_a_arrays[] = $json;
            }


            //build hash of ActionDatum
            $b_data = [];
            foreach ($b as  $b_val) {
                if ($b_val instanceof ActionDatum) {
                    $b_data[strval($b_val->id)] = $b_val;
                }
            }

            $a_data = [];
            foreach ($a as  $a_val) {
                if ($a_val instanceof ActionDatum) {
                    $a_data[strval($a_val->id)] = $a_val;
                }
            }


            foreach ($a as $a_val) {
                if (is_array($a_val)) {
                    $json = json_encode($a_val);
                    if (!in_array($json,$rem_b_arrays)) {
                        $ret[] = $a_val;
                    }
                } elseif ($a_val instanceof ActionDatum) {
                    if (!array_key_exists(strval($a_val->id),$b_data)) {
                        $ret[] = $a_val;
                    }
                } else {
                    if (!in_array($a_val,$b,true)) { $ret[] = $a_val;}
                }
            }

            foreach ($b as $b_val) {
                if (is_array($b_val)) {
                    $json = json_encode($b_val);
                    if (!in_array($json,$rem_a_arrays)) {
                        $ret[] = $b_val;
                    }
                } elseif ($b_val instanceof ActionDatum) {
                    if (!array_key_exists(strval($b_val->id),$a_data)) {
                        $ret[] = $b_val;
                    }
                } else {
                    if (!in_array($b_val,$a,true)) { $ret[] = $b_val;}
                }
            }

        } else {
            //do the hashes get common keys and then use the keys that are not in this list
            $distict_keys = array_intersect_key($a,$b);
            foreach ($a as $a_key => $a_val) {
                if (!in_array($a_key,$distict_keys)) { $ret[$a_key] = $a_val;}
            }

            foreach ($b as $b_key => $b_val) {
                if (!in_array($b_key,$distict_keys)) { $ret[$b_key] = $b_val;}
            }
        }
        return $ret;

    }


    /**
     * each array can have elements, each element can be a key or list:  with primitives, arrays (list or hash) or ActionDatum
     * the result is items in both arrays
     * if one array is a list and the other is a hash, then return empty array
     * if both are lists, then only unique items, any arrays kept have their original structure, so no array merging
     * if both are hashes, then common keys are combined, with array merging (this call recursive) and the others are kept
     * @param array|ActionDatum[] $a
     * @param array|ActionDatum[]  $b
     * @return array|ActionDatum[]
     */
    protected static function mergeUnion(array $a, array $b) : array
    {
        if (array_is_list($a) && !array_is_list($b)) {return [];}
        if (array_is_list($b) && !array_is_list($a)) {return [];}
        $ret = [];
        if (array_is_list($b) && array_is_list($a)) {
            //go through $a for each element, and see if $b has it, this can be a primitive, array, or THingData. if in either then combine with unique values
            $rem = [];

            foreach ($a as $a_val) {
                if (is_array($a_val)) {
                    $json = json_encode($a_val);
                    if (!in_array($json,$rem)) {
                        $ret[] = $a_val;
                        $rem[] = $json;
                    }
                } elseif ($a_val instanceof ActionDatum) {
                    if (!in_array($a_val->id . '-data-key',$rem)) {
                        $ret[] = $a_val;
                        $rem[] = $a_val->id . '-data-key';
                    }
                } else {
                    if (!in_array($a_val,$rem,true)) { $ret[] = $a_val;}
                }
            }

            foreach ($b as $b_val) {
                if (is_array($b_val)) {
                    $json = json_encode($b_val);
                    if (!in_array($json,$rem)) {
                        $ret[] = $b_val;
                        $rem[] = $json;
                    }
                } elseif ($b_val instanceof ActionDatum) {
                    if (!in_array($b_val->id . '-data-key',$rem)) {
                        $ret[] = $b_val;
                        $rem[] = $b_val->id . '-data-key';
                    }
                } else {
                    if (!in_array($b_val,$rem,true)) { $ret[] = $b_val;}
                }
            }
        } else {

            foreach ($a as $a_key => $a_val) {
                $ret[$a_key] = $a_val;
            }

            foreach ($b as $b_key => $b_val) {
                if (!array_key_exists($b_key,$ret)) {
                    $ret[$b_key] = $b_val;
                } else {
                    if (is_array($b_val) && is_array($ret[$b_key]))
                    {
                        $ret[$b_key] = TypeOfMergeLogic::mergeUnion($ret[$b_key],$b_val);
                    }
                    elseif (!is_array($b_val) && !is_array($ret[$b_key]))
                    {
                        $ret[$b_key] = [$ret[$b_key],$b_val];
                    }
                    elseif (is_array($b_val) && !is_array($ret[$b_key]))
                    {
                        if (array_is_list($b_val)) {
                            $temp = $b_val;
                            $temp[] = $ret[$b_key];
                            $ret[$b_key] = $temp  ;
                        }
                        // else do not change
                    }
                    elseif (!is_array($b_val) && is_array($ret[$b_key]))
                    {
                        if (array_is_list($ret[$b_key])) {
                            $temp = $ret[$b_key];
                            $temp[] = $b_val;
                            $ret[$b_key] = $temp;
                        }
                        // else do not change
                    }
                }
            }
        }
        return $ret;
    }
}


