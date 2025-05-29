<?php

namespace App\OpenApi\Results\Types;

use App\Enums\Types\TypeOfApproval;
use App\Models\ElementTypeParent;
use App\OpenApi\Results\ResultBase;
use Carbon\Carbon;
use OpenApi\Attributes as OA;


/**
 * Show details about a parent type
 */
#[OA\Schema(schema: 'TypeParentResponse')]
class TypeParentResponse extends ResultBase
{


    #[OA\Property(title: 'Parent type')]
    public TypeResponse $parent ;



    #[OA\Property(title: 'Approval')]
    public TypeOfApproval $approval;

    #[OA\Property(title: 'Parent since',format: 'date-time')]
    public ?string $parent_at = '';





    public function __construct( ElementTypeParent $given_parent , int $namespace_levels = 0,int $parent_levels = 0,
                                 int $attribute_levels = 0, int $inherited_attribute_levels = 0 )
    {
        parent::__construct();
        $this->parent_at = $given_parent->created_at?
                            Carbon::parse($given_parent->created_at,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;

        $this->approval = $given_parent->parent_type_approval;
        $this->parent = new TypeResponse(given_type: $given_parent->parent_type,
            namespace_levels: $namespace_levels, parent_levels: $parent_levels,
            attribute_levels: $attribute_levels,inherited_attribute_levels: $inherited_attribute_levels);

    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['parent'] = $this->parent;
        $ret['approval'] = $this->approval->value;
        $ret['parent_at'] = $this->parent_at;
        return $ret;
    }

}
