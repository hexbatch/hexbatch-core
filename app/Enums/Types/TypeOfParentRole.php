<?php
namespace App\Enums\Types;
/**
 * postgres enum type_of_server_status
 */
enum TypeOfParentRole : string {

  case DESIGNED = 'designed';
  case LIVE = 'live';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfParentRole {
        $maybe  = TypeOfParentRole::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfParentRole::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


