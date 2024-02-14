<?php
namespace App\Models\Enums\Remotes;
enum RemoteStackMergeDataType : string {

    case UNION_OR = 'union_or';
    case UNION_AND = 'union_and';
    case UNION_XOR = 'union_xor';
    case UNION_OR_REPLACE = 'union_or_replace';
    case UNION_AND_REPLACE = 'union_and_replace';

}
