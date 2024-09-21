<?php
namespace App\Models\Enums\Types;
enum AttributeAccessType : string {
    case NORMAL = 'normal';
    case ELEMENT_PRIVATE = 'element_private';
    case TYPE_PRIVATE = 'type_private';

}

