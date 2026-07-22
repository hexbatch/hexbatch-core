<?php
namespace App\Enums\Attributes;
use App\Data\ApiParams\Enums\EnumTryTrait;

/**
 * postgres enum type_of_live_attribute_behavior
 */

enum TypeOfLiveAttributeBehavior : string {
    use EnumTryTrait;
    case NORMAL = 'normal';
    case FILTER = 'filter';
    case BLOCK = 'block';

}


