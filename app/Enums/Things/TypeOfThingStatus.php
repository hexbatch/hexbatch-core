<?php
namespace App\Enums\Things;
/**
 * postgres enum type_of_thing_status
 */
enum TypeOfThingStatus : string {

  case PENDING = 'pending';
  case FINISHED_APPROVED = 'finished_approved';
  case FINISHED_DENIED = 'finished_denied';
  case ERROR = 'error';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfChildLogic {
        $maybe  = TypeOfChildLogic::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfChildLogic::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


