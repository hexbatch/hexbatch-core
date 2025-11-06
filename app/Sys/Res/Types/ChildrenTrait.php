<?php


namespace App\Sys\Res\Types;

trait ChildrenTrait {
    const CHILD_DECISION_KEY = 'child_decision';

    public static function getDecisionUsingAndLogic(array $children_args) : bool {
        if (empty($children_args)) {return true;}

        foreach ($children_args as $key => $part) {
            if ($key === static::CHILD_DECISION_KEY) {
                if ($part === false) {
                    return false;
                }
            }
            if (is_array($part)) {
                foreach ($part as $p_key => $p_part) {
                    if ($p_key === static::CHILD_DECISION_KEY) {
                        if ($p_part === false) {
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
}
