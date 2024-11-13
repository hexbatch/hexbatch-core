<?php
namespace App\Helpers\Annotations\Access;

/**
 * postgres enum type_of_approval
 */
enum TypeOfAccessMarker : string {

    case NAMESPACE_OWNER = 'namespace_owner';
    case NAMESPACE_ADMIN = 'namespace_admin';
    case NAMESPACE_MEMBER = 'namespace_member';
    case MIXED = 'mixed';
    case IS_PUBLIC = 'is_public';
    case SYSTEM = 'system';

}


