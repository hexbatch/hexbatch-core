<?php
namespace App\Enums\Types;
use Illuminate\Support\Collection;

/**
 * postgres enum type_of_approval
 */
enum TypeOfApproval : string {

    case APPROVAL_NOT_SET = 'approval_not_set';
    case PENDING_DESIGN_APPROVAL = 'pending_design_approval';
    case DESIGN_APPROVED = 'design_approved';
    case DESIGN_DENIED = 'design_denied';
    case PENDING_PUBLISHING_APPROVAL = 'pending_publishing_approval';
    case PUBLISHING_APPROVED = 'publishing_approved';
    case PUBLISHING_DENIED = 'publishing_denied';


    public static function tryFromInput(string|int|bool|null $test ) : TypeOfApproval {
        $maybe  = TypeOfApproval::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfApproval::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

    public static function getFromCollection(Collection $collection,string $param_name)
    :?TypeOfApproval
    {
        if ($collection->has($param_name)) {
            $testy = $collection->get($param_name);
            if (empty($testy)) {return null;}

            if (is_string($testy)) {
                return TypeOfApproval::tryFromInput($testy);
            } elseif ($testy instanceof TypeOfApproval) {
                return  $testy;
            } else {
                throw new \InvalidArgumentException(__("msg.invalid_enum_type",['ref'=>$testy,'enum'=>self::class]));
            }
        }
        return null;
    }
}


