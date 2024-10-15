<?php
namespace App\Enums\Types;
/**
 * postgres enum type_of_lifecycle
 */
enum TypeOfLifecycle : string {

  case DEVELOPING = 'developing';
  case PUBLISHED = 'published';
  case RETIRED = 'retired';
  case SUSPENDED = 'suspended';
  //note suspended only done by server admin, suspended types cannot make new elements , their existing elements are  force destroyed, elsewhere is told about suspension

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfLifecycle {
        $maybe  = TypeOfLifecycle::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfLifecycle::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


