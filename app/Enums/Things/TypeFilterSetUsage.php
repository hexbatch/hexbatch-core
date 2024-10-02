<?php
namespace App\Enums\Things;
/**
 * postgres enum type_filter_set_usage
 */
enum TypeFilterSetUsage : string {

  case NONE = 'none';
  case MUST_MATCH = 'must_match';
  case MUST_EXCLUDE = 'must_exclude';

    public static function tryFromInput(string|int|bool|null $test ) : TypeFilterSetUsage {
        $maybe  = TypeFilterSetUsage::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeFilterSetUsage::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


