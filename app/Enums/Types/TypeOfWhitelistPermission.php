<?php
namespace App\Enums\Types;
/**
 * postgres enum type_of_whitelist_permission
 */
enum TypeOfWhitelistPermission : string {

  case INHERITING = 'inheriting';
  case CREATE_ELEMENTS = 'create_elements';
  case OWN_ELEMENTS = 'own_elements';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfWhitelistPermission {
        $maybe  = TypeOfWhitelistPermission::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfWhitelistPermission::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


