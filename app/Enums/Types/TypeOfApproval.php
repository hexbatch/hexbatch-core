<?php
namespace App\Enums\Types;
use App\Data\ApiParams\Enums\EnumTryTrait;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;
/**
 * postgres enum type_of_approval
 */
#[OA\Schema(title: "Approval stage")]
enum TypeOfApproval : string {
    use EnumTryTrait;
    case APPROVAL_NOT_SET = 'approval_not_set';
    case PENDING_DESIGN_APPROVAL = 'pending_design_approval';
    case DESIGN_APPROVED = 'design_approved';
    case DESIGN_DENIED = 'design_denied';
    case PENDING_PUBLISHING_APPROVAL = 'pending_publishing_approval';
    case PUBLISHING_APPROVED = 'publishing_approved';
    case PUBLISHING_DENIED = 'publishing_denied';
}


