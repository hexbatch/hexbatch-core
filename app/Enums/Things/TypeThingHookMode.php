<?php
namespace App\Enums\Things;
/**
 * postgres enum type_thing_hook_mode
 */
enum TypeThingHookMode : string {
    case NONE = 'none';
    case DEBUG_BREAKPOINT = 'debug_breakpoint';
    case TREE_CREATION_HOOK = 'tree_creation_hook';
    case TREE_STARTING_HOOK = 'tree_starting_hook';
    case NODE_BEFORE_RUNNING_HOOK = 'node_before_running_hook';
    case NODE_AFTER_RUNNING_HOOK = 'node_after_running_hook';


    case TREE_PAUSED_NOTICE = 'tree_paused_notice';
    case TREE_UNPAUSED_NOTICE = 'tree_unpaused_notice';

    case NODE_WAITING_NOTICE = 'node_waiting_notice';
    case NODE_RESUME_NOTICE = 'node_resume_notice';


    case TREE_FINISHED_NOTICE = 'tree_finished_notice';
    case TREE_SUCCESS_NOTICE = 'tree_success_notice';
    case TREE_FAILURE_NOTICE = 'tree_failure_notice';


    case NODE_FAILURE_NOTICE = 'node_failure_notice';
    case NODE_SUCCESS_NOTICE = 'node_success_notice';


    public static function tryFromInput(string|int|bool|null $test ) : TypeThingHookMode {
        $maybe  = TypeThingHookMode::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeThingHookMode::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


