<?php
namespace App\Enums\Things;
/**
 * postgres enum type_of_thing_status
 */
enum TypeOfThingStatus : string {

  case THING_PENDING = 'thing_pending';
  case THING_WAITING = 'thing_waiting'; //when waiting for incoming remote or outside happening
  case THING_SUCCESS = 'thing_success';
  case THING_ERROR = 'thing_error';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfThingStatus {
        $maybe  = TypeOfThingStatus::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfThingStatus::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


