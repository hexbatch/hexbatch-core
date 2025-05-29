<?php
namespace App\Enums\Attributes;
use Illuminate\Support\Collection;

/**
 * postgres enum type_of_server_access
 */
enum TypeOfServerAccess : string {

    case IS_PRIVATE = 'is_private';
    case IS_PUBLIC = 'is_public';
    case IS_PROTECTED = 'is_protected';

    public static function tryFromInput(string|int|bool|null $test ) : ?TypeOfServerAccess {
        if ($test === null) {return null;}
        $maybe  = TypeOfServerAccess::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfServerAccess::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

    public static function getFromCollection(Collection $collection,string $param_name)
    :?TypeOfServerAccess
    {
        if ($collection->has($param_name)) {
            $testy = $collection->get($param_name);
            if (empty($testy)) {return null;}

            if (is_string($testy)) {
                return TypeOfServerAccess::tryFromInput($testy);
            } elseif ($testy instanceof TypeOfServerAccess) {
                return  $testy;
            } else {
                throw new \InvalidArgumentException(__("msg.invalid_enum_type",['ref'=>$testy,'enum'=>self::class]));
            }
        }
        return null;
    }
}


