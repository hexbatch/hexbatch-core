<?php

namespace App\OpenApi\Results\Attributes;

use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Types\TypeOfApproval;
use App\Models\Attribute;
use App\OpenApi\Common\HexbatchUuid;
use App\OpenApi\Results\Bounds\LocationResponse;
use App\OpenApi\Results\ResultBase;
use App\OpenApi\Results\Types\TypeResponse;
use Carbon\Carbon;
use OpenApi\Attributes as OA;


/**
 * Show details about an attribute
 */
#[OA\Schema(schema: 'AttributeResponse')]
class AttributeResponse extends ResultBase
{

    #[OA\Property(title: 'Attribute uuid',type: HexbatchUuid::class)]
    public string $uuid = '';

    #[OA\Property(title: 'Attribute name')]
    public string $name = '';

    #[OA\Property(title: 'Attribute short name')]
    public string $short_name = '';

    #[OA\Property(title: 'Owning type uuid',type: HexbatchUuid::class)]
    public string $owning_type_uuid = '';

    #[OA\Property(title: 'Owning type')]
    public ?TypeResponse $owner_type = null;

    #[OA\Property(title: 'Parent')]
    public ?AttributeResponse $parent = null;


    #[OA\Property(title: 'Design')]
    public ?AttributeResponse $design = null;

    #[OA\Property(title: 'Location')]
    public ?LocationResponse $location = null;


    #[OA\Property(title: 'Is system')]
    public bool $is_system ;

    #[OA\Property(title: 'Is final')]
    public bool $is_final ;

    #[OA\Property(title: 'Is abstract')]
    public bool $is_abstract ;


    #[OA\Property( title: 'Access')]
    public TypeOfServerAccess $access  ;

    #[OA\Property( title: 'Value policy')]
    public TypeOfElementValuePolicy $value_policy ;

    #[OA\Property( title: 'Value policy')]
    public TypeOfApproval $approval ;


    #[OA\Property(title: 'Read json path')]
    public ?string $read_json_path ;

    #[OA\Property(title: 'Validation json path')]
    public ?string $validation_json_path ;


    #[OA\Property( title: "Default value", items: new OA\Items(), nullable: true)]
    protected ?array $default_value = null;

    #[OA\Property(title: 'Attribute created at',format: 'date-time')]
    public ?string $created_at = '';




    public function __construct(
         Attribute $given_attribute, int $attribute_levels = 0,int $owning_type_levels = 0,int $design_levels = 0
    )
    {
        parent::__construct();
        $this->uuid = $given_attribute->ref_uuid;
        $this->name = $given_attribute->getName(short_name: false);
        $this->short_name = $given_attribute->getName();
        if ($owning_type_levels) {
            $this->owner_type = new TypeResponse(given_type: $given_attribute->type_owner);
        }

        $this->owning_type_uuid = $given_attribute->type_owner->ref_uuid;

        if($attribute_levels) {
            if ($given_attribute->attribute_parent) {
                $this->parent = new AttributeResponse(
                    given_attribute: $given_attribute->attribute_parent,attribute_levels: $attribute_levels - 1);
            }
        }

        if($design_levels) {
            /** @uses Attribute::attribute_design() */
            if ($given_attribute->attribute_design) {
                $this->design = new AttributeResponse(
                    given_attribute: $given_attribute->attribute_design,attribute_levels: $design_levels - 1);
            }
        }

        if($given_attribute->attribute_shape_bound) {
            $this->location = new LocationResponse(given_location: $given_attribute->attribute_shape_bound);
        }
        $this->is_system = $given_attribute->is_system;
        $this->is_abstract = $given_attribute->is_abstract;
        $this->is_final = $given_attribute->is_final_attribute;
        $this->access = $given_attribute->server_access_type;
        $this->value_policy = $given_attribute->value_policy;
        $this->approval = $given_attribute->attribute_approval;
        $this->read_json_path = $given_attribute->read_json_path;
        $this->validation_json_path = $given_attribute->validate_json_path;
        $this->default_value = $given_attribute->attribute_default_value?->getArrayCopy();

        $this->created_at = $given_attribute->created_at?
            Carbon::parse($given_attribute->created_at,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;

    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['uuid'] = $this->uuid;
        $ret['name'] = $this->name;
        $ret['short_name'] = $this->short_name;
        $ret['is_system'] = $this->is_system;
        $ret['is_abstract'] = $this->is_abstract;
        $ret['is_final'] = $this->is_final;
        $ret['access'] = $this->access->value;
        $ret['value_policy'] = $this->value_policy->value;
        $ret['approval'] = $this->approval->value;
        $ret['read_json_path'] = $this->read_json_path;
        $ret['validation_json_path'] = $this->validation_json_path;
        $ret['default_value'] = $this->default_value;
        $ret['created_at'] = $this->created_at;

        $ret['owning_type_uuid'] = $this->owning_type_uuid;
        if ($this->owner_type) {
            $ret['owner_type'] = $this->owner_type;
        }

        if ($this->parent) {
            $ret['parent'] = $this->parent;
        }

        if ($this->location) {
            $ret['location'] = $this->location;
        }

        if ($this->design) {
            $ret['parent'] = $this->design;
        }



        return $ret;
    }

}
