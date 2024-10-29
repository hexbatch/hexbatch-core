<?php
namespace App\Enums\Things;
/**
 * postgres enum type_of_thing_data_source
 */
enum TypeOfThingDataSource : string {

  case NOT_SET = 'not_set';
  case FROM_CHILDREN = 'from_children'; //when waiting for incoming remote, event or signal
  case FROM_CURRENT = 'from_current';
  case FROM_ACTION_SETUP = 'from_action_setup';
  case FROM_API_SETUP = 'from_api_setup';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfThingDataSource {
        $maybe  = TypeOfThingDataSource::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfThingDataSource::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


