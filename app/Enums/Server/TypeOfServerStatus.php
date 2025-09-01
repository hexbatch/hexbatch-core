<?php
namespace App\Enums\Server;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;
/**
 * postgres enum type_of_server_status
 */
#[OA\Schema(title: "Server status")]
enum TypeOfServerStatus : string {

  case UNKNOWN_SERVER = 'unknown_server';
  case PENDING_SERVER = 'pending_server';
  case ALLOWED_SERVER = 'allowed_server';
  case PAUSED_SERVER = 'paused_server';
  case BLOCKED_SERVER = 'blocked_server'; //no data exchange

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfServerStatus {
        $maybe  = TypeOfServerStatus::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfServerStatus::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

    public static function getFromCollection(Collection $collection,string $param_name)
    :?TypeOfServerStatus
    {
        if ($collection->has($param_name)) {
            $testy = $collection->get($param_name);
            if (empty($testy)) {return null;}

            if (is_string($testy)) {
                return TypeOfServerStatus::tryFromInput($testy);
            } elseif ($testy instanceof TypeOfServerStatus) {
                return  $testy;
            } else {
                throw new \InvalidArgumentException(__("msg.invalid_enum_type",['ref'=>$testy,'enum'=>self::class]));
            }
        }
        return null;
    }

}


