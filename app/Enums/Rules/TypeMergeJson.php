<?php
namespace App\Enums\Rules;
/**
 * postgres enum type_merge_json
 */
enum TypeMergeJson : string {
    case OVERWRITE = 'overwrite';
    case OR_MERGE = 'or_merge';
    case AND_MERGE = 'and_merge';
    case XOR_MERGE = 'xor_merge';
    case OLDEST = 'oldest';
    case NEWEST = 'newest';
    case OVERWRITE_ADD = 'overwrite_add'; //overwrites unless matching keys have numeric, then will add them and put sum in destination

    case OVERWRITE_SUBTRACT = 'overwrite_subtract';

    public static function tryFromInput(string|int|bool|null $test ) : TypeMergeJson {
        $maybe  = TypeMergeJson::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeMergeJson::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


