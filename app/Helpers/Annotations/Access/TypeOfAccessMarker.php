<?php
namespace App\Helpers\Annotations\Access;

/**
 * postgres enum type_of_approval
 */
enum TypeOfAccessMarker : string {

    case NAMESPACE_OWNER = 'namespace_owner';
    case NAMESPACE_ADMIN = 'namespace_admin';
    case NAMESPACE_MEMBER = 'namespace_member';
    case TYPE_OWNER = 'type_owner';
    case TYPE_ADMIN = 'type_admin';
    case TYPE_MEMBER = 'type_member';

    case LINK_ADMIN = 'link_admin';
    case LINK_MEMBER = 'link_member';
    case ELEMENT_OWNER = 'element_owner';
    case ELEMENT_ADMIN = 'element_admin';
    case ELEMENT_MEMBER = 'element_member';
    case MIXED = 'mixed';
    case IS_PUBLIC = 'is_public'; //not logged in
    case SYSTEM = 'system';

}


