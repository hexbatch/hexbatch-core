<?php
namespace App\Enums\Types;
/**
 * postgres enum type_of_server_status
 */
enum TypeOfWhitelistPermission : string {

  case INHERITING = 'inheriting';
  case CREATE_ELEMENTS = 'create_elements';
  case OWN_ELEMENTS = 'own_elements';
  case READ_ELEMENTS = 'read_elements';
  case WRITE_ELEMENTS = 'write_elements';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfWhitelistPermission {
        $maybe  = TypeOfWhitelistPermission::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfWhitelistPermission::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


