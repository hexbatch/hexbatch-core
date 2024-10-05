<?php
namespace App\Enums\Types;
/**
 * postgres enum type_of_server_status
 */
enum TypeOfLifecycle : string {

  case DEVELOPING = 'developing';
  case PUBLISHED = 'published';
  case RETIRED = 'retired';
  case SUSPENDED = 'suspended';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfLifecycle {
        $maybe  = TypeOfLifecycle::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfLifecycle::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


