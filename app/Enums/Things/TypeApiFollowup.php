<?php
namespace App\Enums\Things;
/**
 * postgres enum type_api_followup
 */
enum TypeApiFollowup : string {
    case NO_FOLLOWUP = 'no_followup';
    case DIRECT_FOLLOWUP = 'direct_followup';
    case POLLED_FOLLOWUP = 'polled_followup';
    case FOLLOWUP_CALLBACK_SUCCESSFUL = 'followup_callback_successful';
    case FOLLOWUP_CALLBACK_ERROR = 'followup_callback_error';
    case REMOTE_IN_PENDING = 'remote_wait_pending';
    case REMOTE_IN_COMPLETED = 'remote_wait_completed';
    case REMOTE_IN_ERROR = 'remote_wait_error';
    case REMOTE_OUT_PENDING = 'remote_pending';
    case REMOTE_OUT_SUCCESS = 'remote_success';
    case REMOTE_OUT_ERROR = 'remote_error';

    public static function tryFromInput(string|int|bool|null $test ) : TypeApiFollowup {
        $maybe  = TypeApiFollowup::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeApiFollowup::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}

