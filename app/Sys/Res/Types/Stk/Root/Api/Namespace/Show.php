<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Data\ApiParams\Data\Namespaces\UserNamespaceData;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Api;


class Show extends Api\NamespaceApi
{
    const UUID = 'b4d38538-4289-4752-8670-d7124af5a442';
    const TYPE_NAME = 'api_namespace_show';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class
    ];

    public static function showNamespace(UserNamespace $caller,UserNamespace $target, bool $b_public_only = false) : UserNamespaceData {

       if ($b_public_only) {
           $target->loadMissing(
               'home_set',
               'public_element',
               'namespace_base_type',
           );
       } else {
           if ($target->isNamespaceAdmin($caller)) {
               $target->loadMissing(
                   'private_element',
               );
           }
           $target->loadMissing(
               'namespace_admins',
               'home_set',
               'home_set.element_members',
               'public_element',
               'namespace_base_type',
               'namespace_base_type.type_exposed_attributes',
               'owner_user'
           );
       }

        return UserNamespaceData::MakingUsingCodeArray($target);
    }

}

