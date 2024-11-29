<?php
namespace App\Enums\Rules;
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


    /**
     * this is union except the lower rank is kept for common keys:
     *  json if object combine the keys, the higher rank's value is kept,  if array then all elements merged but are unique in array
     *  data: combine all child data
     */
    case UNION_NEWEST = 'union_newest'; //union, keys combined, arrays combined


    /**
     * union_newest unless matching keys have numeric, then will add them and put sum in destination
     */
    case UNION_NEWEST_ADD = 'union_newest_add';

    /**
     * union unless matching keys have numeric, then will add them and put sum in destination
     */
    case UNION_ADD = 'union_add';

    /**
     * union_newest unless matching keys have numeric, then will subtract the newest from the oldest and put diff in destination
     */
    case UNION_NEWEST_SUB = 'union_newest_sub';

    /**
     * union unless matching keys have numeric, then will subtract the oldest from the newest and put diff in destination
     */
    case UNION_SUB = 'union_sub';



    public static function tryFromInput(string|int|bool|null $test ) : TypeOfMergeLogic {
        $maybe  = TypeOfMergeLogic::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfMergeLogic::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


