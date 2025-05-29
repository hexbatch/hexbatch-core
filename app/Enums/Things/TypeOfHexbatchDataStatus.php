<?php
namespace App\Enums\Things;
/**
 * no db link
 */
enum TypeOfHexbatchDataStatus : string {

  case THING_BUILDING = 'thing_building';
  case THING_PENDING = 'thing_pending';
  case THING_WAITING = 'thing_waiting'; //when waiting for incoming remote, event or signal
  case THING_PAUSED = 'thing_paused'; //-- to stop auto running of things, when the debugger is single stepping or breakpoint
  case THING_SUCCESS = 'thing_success';
  case THING_ERROR = 'thing_error';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfHexbatchDataStatus {
        $maybe  = TypeOfHexbatchDataStatus::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfHexbatchDataStatus::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


