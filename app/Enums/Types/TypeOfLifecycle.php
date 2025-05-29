<?php
namespace App\Enums\Types;
use Illuminate\Support\Collection;

/**
 * postgres enum type_of_lifecycle
 */
enum TypeOfLifecycle : string {

  case DEVELOPING = 'developing';
  case PUBLISHED = 'published';
  case RETIRED = 'retired';
  case SUSPENDED = 'suspended';
  //note suspended only done by server admin, suspended types cannot make new elements , their existing elements are  force destroyed, elsewhere is told about suspension

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfLifecycle {
        $maybe  = TypeOfLifecycle::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfLifecycle::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

    public static function getFromCollection(Collection $collection,string $param_name)
        :?TypeOfLifecycle
    {
        if ($collection->has($param_name)) {
            $testy = $collection->get($param_name);
            if (empty($testy)) {return null;}

            if (is_string($testy)) {
                return TypeOfLifecycle::tryFromInput($testy);
            } elseif ($testy instanceof TypeOfLifecycle) {
                return  $testy;
            } else {
                throw new \InvalidArgumentException(__("msg.invalid_enum_type",['ref'=>$testy,'enum'=>self::class]));
            }
        }
        return null;
    }
}


