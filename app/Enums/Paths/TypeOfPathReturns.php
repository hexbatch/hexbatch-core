<?php
namespace App\Enums\Paths;
use OpenApi\Attributes as OA;
/**
 * postgres enum type_of_path_returns
 */
enum TypeOfPathReturns : string {

  case EXISTS = 'exists';
  case TYPE = 'type';
  case VALUES = 'values';

  case SET = 'set';
  case THING = 'thing';
  case RULE = 'rule';
  case ELEMENT = 'element';
  case ATTRIBUTE = 'attribute';
  case NAMESPACE = 'namespace' ;
  case MAX = 'max' ;
  case MIN = 'min' ;
  case AVERAGE = 'average' ;
  case COUNT_COMPARE = 'count_compare' ;
  case COUNT = 'count' ;


    public static function tryFromInput(string|int|bool|null $test ) : TypeOfPathReturns {
        $maybe  = TypeOfPathReturns::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfPathReturns::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


