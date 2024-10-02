<?php
namespace App\Enums\Things;
/**
 * postgres enum type_user_followup
 */
enum TypeUserFollowup : string {
    case NOTHING = 'nothing';
  case DIRECT = 'direct';
  case POLLED = 'polled';
  case CALLBACK_SUCCESSFUL = 'callback_successful';
  case CALLBACK_ERROR = 'callback_error';

    public static function tryFromInput(string|int|bool|null $test ) : TypeUserFollowup {
        $maybe  = TypeUserFollowup::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeUserFollowup::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


