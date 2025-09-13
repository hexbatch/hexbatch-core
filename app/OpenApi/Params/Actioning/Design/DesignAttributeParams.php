<?php

namespace App\OpenApi\Params\Actioning\Design;


use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Helpers\Utilities;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\LocationBound;
use App\Models\UserNamespace;
use App\OpenApi\ApiThingBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

/*
 * Attribute Design
 *  uuid : when editing an existing attribute
 *  type_uuid: each attribute is defined as part of a type, but can be inherited by attributes elsewhere
 *  design_uuid: visually represents the attribute
 *  parent_uuid: this is set to pending, and the parent is notified to approve. If setting new parent, that one is asked to approve if not public domain
 *  location_uuid: attributes can have a shape
 *  is_final: cannot be a parent
 *  is_abstract: not usable by itself, must have a child
 *  access: sets access across different servers
 *  value_policy: determines if the attribute can have multiple values for the same or all elements that use it
 *  read_json_path: if this is used, when the attribute value is always filtered by this
 *  validate_json_path: if this is used, when the attribute value is validated before being set
 *  default_value : if set, this is the default value before writing
 *  attribute_name: has to be unique in the namespace
 *  unset_parent : when editing an existing attribute and want to remove the parent
 */
#[OA\Schema(schema: 'DesignAttributeParams')]
class DesignAttributeParams extends ApiThingBase
{

    protected ?string $uuid = null;

    #[OA\Property(title: 'Type',description: 'Attributes must have a type. This can the full name or uuid')]
    protected ?string $type_uuid = null;

    #[OA\Property(title: 'Design',description: 'Attributes can have a design. This will be representing the attribute in some displays. This can the full name or uuid')]
    protected ?string $design_uuid = null;

    #[OA\Property(title: 'Parent',description: 'Attributes can have a parent. This can the full name or uuid')]
    protected ?string $parent_uuid = null;

    #[OA\Property(title: 'Location',description: 'Attributes can have a shape or map. This can restrict the sets they are put in, if those sets do not allow any overlap. This can be set by the uuid or full location name')]
    protected ?string $location_uuid = null;

    #[OA\Property(title: 'Is final',description: 'This attribute cannot have children or descendants of other attributes')]
    protected bool $is_final = false;

    #[OA\Property(title: 'Is abstract',description: 'This attribute is not usable by itself, must have a child')]
    protected bool $is_abstract = false;
    #[OA\Property(title: 'Unset Parent',description: 'When editing an existing attribute and want to remove the parent')]
    protected bool $unset_parent = false;

    #[OA\Property(title: 'Access Policy',description:'Sets data visibility')]
    protected TypeOfServerAccess $access ;

    #[OA\Property(title: 'Value Policy',description:'Sets data visibility')]
    protected TypeOfElementValuePolicy $value_policy ;

    #[OA\Property(title: 'Read Filter',description:'When reading data set by this attribute, use this conversion or filter')]
    protected ?string $read_json_path = null;
    #[OA\Property(title: 'Data Validation',description:'When setting data controlled by this attribute, use this conversion or filter')]
    protected ?string $validate_json_path = null;

    #[OA\Property( title: "Default data (optional)", items: new OA\Items(), nullable: true)]
    /** @var mixed[] $default_value */
    protected array $default_value = [];

    #[OA\Property(title: 'Attribute name',maxLength: 40,minLength: 3)]
    protected ?string $attribute_name = null;


    public function __construct(
        protected ?ElementType       $given_type = null,
        protected ?Attribute       $given_attribute = null,
        protected ?Attribute       $given_design = null,
        protected ?Attribute       $parent = null,
        protected ?LocationBound          $location = null,
        protected ?UserNamespace $namespace = null,

    )
    {
        parent::__construct();
        $this->type_uuid = $this->given_type?->ref_uuid;
       $this->uuid = $this->given_attribute?->ref_uuid;
       $this->design_uuid = $this->given_design?->ref_uuid;
       $this->parent_uuid = $this->parent?->ref_uuid;
       $this->location_uuid = $this->location?->ref_uuid;

    }


    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);


        if (!$this->given_attribute) {
            if ($col->has('uuid') && $col->get('uuid')) {
                $this->given_attribute = Attribute::resolveAttribute(value: $col->get('uuid'));
                $this->uuid = $this->given_attribute->ref_uuid;
            }
        }


        if (!$this->parent) {
            if ($col->has('parent_uuid') && $col->get('parent_uuid')) {
                $this->parent = Attribute::resolveAttribute(value: $col->get('parent_uuid'));
                $this->parent_uuid = $this->parent->ref_uuid;
            }
        }

        if (!$this->given_type) {
            if ($col->has('type_uuid') && $col->get('type_uuid')) {
                $this->given_type = ElementType::resolveType(value: $col->get('type_uuid'),context_namespace_uuid: $this->namespace?->ref_uuid);
                $this->type_uuid = $this->given_type->ref_uuid;
            }
        }

        if (!$this->given_design) {
            if ($col->has('design_uuid') && $col->get('design_uuid')) {
                $this->given_design = Attribute::resolveAttribute(value: $col->get('design_uuid'));
                $this->design_uuid = $this->given_design->ref_uuid;
            }
        }

        if (!$this->location) {
            if ($col->has('location_uuid') && $col->get('location_uuid')) {
                $this->location = LocationBound::resolveLocation(value: $col->get('location_uuid'));
                $this->location_uuid = $this->location->ref_uuid;
            }
        }


        if ($col->has('is_final')) {
            $this->is_final = Utilities::boolishToBool($col->get('is_final'));
        }

        if ($col->has('is_abstract')) {
            $this->is_abstract = Utilities::boolishToBool($col->get('is_abstract'));
        }

        if ($col->has('unset_parent')) {
            $this->unset_parent = Utilities::boolishToBool($col->get('unset_parent'));
        }

        if ($col->has('access') && $col->get('access')) {
            $this->access = TypeOfServerAccess::tryFromInput($col->get('access'));
        }

        if ($col->has('value_policy') && $col->get('value_policy')) {
            $this->value_policy = TypeOfElementValuePolicy::tryFromInput($col->get('value_policy'));
        }

        if ($col->has('read_json_path') && $col->get('read_json_path')) {
            $this->read_json_path = (string)$col->get('read_json_path');
        }

        if ($col->has('validate_json_path') && $col->get('validate_json_path')) {
            $this->validate_json_path = (string)$col->get('validate_json_path');
        }

        if ($col->has('default_value') && $col->get('default_value')) {
            $raw_default = $col->get('default_value');
            if (!is_array($raw_default)) {
                if (is_object($raw_default)) {
                    $raw_default = Utilities::toArrayOrNull($raw_default);
                } else {
                    $raw_default = [$raw_default];
                }
            }
            $this->default_value = $raw_default;
        }

        if ($col->has('attribute_name') && $col->get('attribute_name')) {
            $this->attribute_name = (string)$col->get('attribute_name');
        }


    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['uuid'] = $this->uuid;
        $ret['type_uuid'] = $this->type_uuid;
        $ret['design_uuid'] = $this->design_uuid;
        $ret['parent_uuid'] = $this->parent_uuid;
        $ret['location_uuid'] = $this->location_uuid;
        $ret['is_final'] = $this->is_final;
        $ret['is_abstract'] = $this->is_abstract;
        $ret['unset_parent'] = $this->unset_parent;
        $ret['access'] = $this->access->value;
        $ret['value_policy'] = $this->value_policy->value;
        $ret['read_json_path'] = $this->read_json_path;
        $ret['validate_json_path'] = $this->validate_json_path;
        $ret['default_value'] = $this->default_value;

        return $ret;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getTypeUuid(): ?string
    {
        return $this->type_uuid;
    }

    public function getDesignUuid(): ?string
    {
        return $this->design_uuid;
    }

    public function getParentUuid(): ?string
    {
        return $this->parent_uuid;
    }

    public function getLocationUuid(): ?string
    {
        return $this->location_uuid;
    }

    public function isFinal(): bool
    {
        return $this->is_final;
    }

    public function isAbstract(): bool
    {
        return $this->is_abstract;
    }

    public function isUnsetParent(): bool
    {
        return $this->unset_parent;
    }

    public function getAccess(): ?TypeOfServerAccess
    {
        return $this->access;
    }

    public function getValuePolicy(): ?TypeOfElementValuePolicy
    {
        return $this->value_policy;
    }

    public function getReadJsonPath(): ?string
    {
        return $this->read_json_path;
    }

    public function getValidateJsonPath(): ?string
    {
        return $this->validate_json_path;
    }

    public function getDefaultValue(): array
    {
        return $this->default_value;
    }

    public function getAttributeName(): ?string
    {
        return $this->attribute_name;
    }

    public function getGivenType(): ?ElementType
    {
        return $this->given_type;
    }

    public function getGivenAttribute(): ?Attribute
    {
        return $this->given_attribute;
    }

    public function getGivenDesign(): ?Attribute
    {
        return $this->given_design;
    }

    public function getParent(): ?Attribute
    {
        return $this->parent;
    }

    public function getLocation(): ?LocationBound
    {
        return $this->location;
    }

    public function getNamespaceUuid(): ?string
    {
        return $this->namespace?->ref_uuid;
    }









}
