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
    case TREE_PAUSED_HOOK = 'tree_paused_hook';
    case TREE_UNPAUSED_HOOK = 'tree_unpaused_hook';
    case TREE_FINISHED_HOOK = 'tree_finished_hook';
    case TREE_SUCCESS_HOOK = 'tree_success_hook';
    case TREE_FAILURE_HOOK = 'tree_failure_hook';
    case NODE_BEFORE_RUNNING_HOOK = 'node_before_running_hook';
    case NODE_AFTER_RUNNING_HOOK = 'node_after_running_hook';
    case NODE_FAILURE_HOOK = 'node_failure_hook';
    case NODE_SUCCESS_HOOK = 'node_success_hook';


    public static function tryFromInput(string|int|bool|null $test ) : TypeThingHookMode {
        $maybe  = TypeThingHookMode::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeThingHookMode::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


