<?php

namespace App\Enums\Rules;
/**
 * postgres enum type_of_logic
 */
enum TypeOfLogic: string
{

    case NOP = 'nop';
    case AND = 'and';
    case OR = 'or';
    case OR_ALL = 'or_all'; //no shortcutting the other or parts if one is truthful, this is the same as making a pipe combing all data from sources
    case XOR = 'xor';
    case NAND = 'nand';
    case NOR = 'nor';
    case NOR_ALL = 'nor_all'; //no shortcutting the other nor parts if one is false, this is the same as making a filter removing source from other data
    case XNOR = 'xnor';
    case ALWAYS_TRUE = 'always_true';
    case ALWAYS_FALSE = 'always_false';



    public static function tryFromInput(string|int|bool|null $test): TypeOfLogic
    {
        $maybe = TypeOfLogic::tryFrom($test);
        if (!$maybe) {
            $delimited_values = implode('|', array_column(TypeOfLogic::cases(), 'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum", ['ref' => $test, 'enum_list' => $delimited_values]));
        }
        return $maybe;
    }
}


