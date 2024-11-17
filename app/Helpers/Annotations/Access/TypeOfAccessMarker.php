<?php
namespace App\Helpers\Annotations\Access;

/**
 * postgres enum type_of_approval
 */
enum TypeOfAccessMarker : string {

    case DEFAULT_NAMESPACE_OWNER = 'default_namespace_owner';
    case NAMESPACE_OWNER = 'namespace_owner';
    case NAMESPACE_ADMIN = 'namespace_admin';
    case NAMESPACE_MEMBER = 'namespace_member';
    case TYPE_OWNER = 'type_owner';
    case TYPE_ADMIN = 'type_admin';
    case TYPE_MEMBER = 'type_member';

    case LINK_OWNER = 'link_owner';
    case LINK_MEMBER = 'link_member';
    case ELEMENT_OWNER = 'element_owner';
    case ELEMENT_ADMIN = 'element_admin';
    case ELEMENT_MEMBER = 'element_member';

    case SET_OWNER = 'set_owner';
    case SET_ADMIN = 'set_admin';
    case SET_MEMBER = 'set_member';
    case MIXED = 'mixed';
    case IS_PUBLIC = 'is_public'; //not logged in
    case SYSTEM = 'system';
    case CALLING_SERVER = 'calling_server';

    case PATH_OWNER = 'path_owner';
    case PATH_ADMIN = 'path_admin';
    case PATH_MEMBER = 'path_member';

}


