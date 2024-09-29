<?php
namespace App\Enums\Rules;
/**
 * postgres enum type_of_child_logic
 */
enum TypeOfChildLogic : string {

  case AND = 'and';
  case OR = 'or';
  case XOR = 'xor';
  case NAND = 'nand';
  case NOR = 'nor';
  case XNOR = 'xnor';
  case ALWAYS_TRUE = 'always_true';
  case ALWAYS_FALSE = 'always_false';

}


