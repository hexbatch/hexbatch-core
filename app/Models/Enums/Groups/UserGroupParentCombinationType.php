<?php
namespace App\Models\Enums\Groups;
enum UserGroupParentCombinationType : string {
    case NONE = 'none';
    case PARENT_UNION = 'parent_union';
    case PARENT_INTERSECTION = 'parent_intersection';
}
