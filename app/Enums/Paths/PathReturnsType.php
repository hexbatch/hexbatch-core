<?php
namespace App\Enums\Paths;
/**
 * postgres enum time_comparison_type
 */
enum PathReturnsType : string {

  case EXISTS = 'exists';
  case TYPE = 'type';
  case ELEMENT = 'element';
  case ATTRIBUTE = 'attribute';
  case NAMESPACE = 'namespace' ;
  case COUNT = 'count' ;

    public static function tryFromInput(string|int|bool|null $test ) : PathReturnsType {
        $maybe  = PathReturnsType::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(PathReturnsType::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


