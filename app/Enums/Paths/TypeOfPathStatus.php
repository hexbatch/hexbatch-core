<?php
namespace App\Enums\Paths;
/**
 * postgres enum type_of_path_status
 */

enum TypeOfPathStatus : string {

  case DESIGN = 'design';
  case ready = 'ready';
  case error = 'error';
  case sabotaged = 'sabotaged';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfPathStatus {
        $maybe  = TypeOfPathStatus::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfPathStatus::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


