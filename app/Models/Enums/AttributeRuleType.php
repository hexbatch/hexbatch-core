<?php
namespace App\Models\Enums;
enum AttributeRuleType : string {

    case INACTIVE = 'inactive';
    case ALLERGY = 'allergy';
    case AFFINITY = 'affinity';
    case READ = 'read';
    case WRITE = 'write';
    case REQUIRED = 'required';
    case FORBIDDEN = 'forbidden';

}


