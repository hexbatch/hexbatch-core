<?php
namespace App\Enums\Types;
/**
 * postgres enum type_of_server_status
 */
enum TypeOfApproval : string {

  case APPROVAL_NOT_SET = 'approval_not_set';
  case AUTOMATIC = 'automatic';
  case PENDING = 'pending';
  case DENIED = 'denied';
  case APPROVED = 'approved';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfApproval {
        $maybe  = TypeOfApproval::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfApproval::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


