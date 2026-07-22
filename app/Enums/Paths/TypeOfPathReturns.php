<?php
namespace App\Enums\Paths;
use App\Data\ApiParams\Enums\EnumTryTrait;

/**
 * postgres enum type_of_path_returns
 */
enum TypeOfPathReturns : string {
    use EnumTryTrait;
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




}


