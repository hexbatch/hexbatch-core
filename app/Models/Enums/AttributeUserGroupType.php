<?php
namespace App\Models\Enums;
enum AttributeUserGroupType : string {
    case INACTIVE = 'inactive';
    case READ = 'read';
    case WRITE = 'write';
    case USAGE = 'usage';

}



