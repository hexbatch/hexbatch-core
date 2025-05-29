<?php
namespace App\Enums\Things;
/**
 * postgres enum type_hooked_thing_status
 */
enum TypeHookedThingStatus : string {
    case NONE = 'none';
    case WAITING_FOR_THING = 'waiting_for_thing';
    case WAITING_FOR_HOOK = 'waiting_for_hook';
    case WAITING_FOR_UPDATE = 'waiting_for_update';
    case CALLBACK_ERROR = 'callback_error';
    case HOOK_SUCCESS = 'hook_success';
    case HOOK_FAIL = 'hook_fail';
    case HOOK_COMPLETE_WITH_ERROR = 'hook_complete_with_error';
    case HOOK_COMPLETE = 'hook_complete';


    public static function tryFromInput(string|int|bool|null $test ) : TypeHookedThingStatus {
        $maybe  = TypeHookedThingStatus::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeHookedThingStatus::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


