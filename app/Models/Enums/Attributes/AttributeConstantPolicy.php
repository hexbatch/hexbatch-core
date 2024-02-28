<?php
namespace App\Models\Enums\Attributes;
enum AttributeConstantPolicy : string {
    case NOT_CONSTANT = 'not_constant';
    case ALWAYS_CONSTANT = 'always_constant';
    case CONSTANT_AFTER_WRITE = 'constant_after_write';
}



