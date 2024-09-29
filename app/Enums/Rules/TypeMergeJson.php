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


}


