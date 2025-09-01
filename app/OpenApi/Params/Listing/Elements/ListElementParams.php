<?php

namespace App\OpenApi\Params\Listing\Elements;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Attribute;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\LocationBound;
use App\Models\Phase;
use App\Models\TimeBound;
use App\Models\UserNamespace;
use App\Rules\NamespaceMemberReq;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'ListElementParams')]
class ListElementParams extends ShowElementParams
{

    #[OA\Property(title: 'Namespace',description: 'The element is in this namespace. Can be uuid or name')]
    protected ?string $namespace_ref = null;


    #[OA\Property(title: 'Type',description: 'The element contains this type. Can be uuid or name')]
    protected ?string $type_ref = null;

    #[OA\Property(title: 'Attribute',description: 'The element contains this attribute. Can be uuid or name')]
    protected ?string $attribute_ref = null;

    #[OA\Property(title: 'Shape',description: 'The element contains this shape or map. Can be uuid or name')]
    protected ?string $shape_ref = null;

    #[OA\Property(title: 'Schedule',description: 'The element has this schedule. Can be uuid or name')]
    protected ?string $schedule_ref = null;

    #[OA\Property(title: 'Set',description: 'The element is in this set')]
    protected ?string $set_ref = null;


    #[OA\Property(title: 'Is set',description: 'The element is|is_not a set')]
    protected ?bool $is_set = null;


    public function __construct(
        protected ?UserNamespace $given_namespace = null,
        protected ?LocationBound $given_location = null,
        protected ?Attribute $given_attribute = null,
        protected ?TimeBound $given_schedule = null,
        protected ?ElementType $given_type = null,
        protected ?ElementSet $given_set = null,
        protected ?Phase    $working_phase = null,
    )
    {
        parent::__construct();
        $this->namespace_ref = $this->given_namespace?->ref_uuid;
        $this->shape_ref = $this->given_location?->ref_uuid;
        $this->schedule_ref = $this->given_schedule?->ref_uuid;
        $this->attribute_ref = $this->given_attribute?->ref_uuid;
        $this->type_ref = $this->given_type?->ref_uuid;
        $this->set_ref = $this->given_set?->ref_uuid;
    }

    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col,$do_validation);

        if (!$this->given_namespace) {
            $this->namespace_ref = static::stringFromCollection(collection: $col,param_name: 'namespace_ref');
            $this->given_namespace = UserNamespace::resolveNamespace(value: $this->namespace_ref);
            $this->namespace_ref = $this->given_namespace->ref_uuid;
        }

        if (!$this->given_location) {
            $this->shape_ref = static::stringFromCollection(collection: $col,param_name: 'shape_ref');
            $this->given_location = LocationBound::resolveLocation(value: $this->shape_ref);
            $this->shape_ref = $this->given_location->ref_uuid;
        }

        if (!$this->given_schedule) {
            $this->schedule_ref = static::stringFromCollection(collection: $col,param_name: 'shape_ref');
            $this->given_schedule = TimeBound::resolveSchedule(value: $this->schedule_ref);
            $this->schedule_ref = $this->given_schedule->ref_uuid;
        }

        if (!$this->given_attribute) {
            $this->attribute_ref = static::stringFromCollection(collection: $col,param_name: 'attribute_ref');
            $this->given_attribute = Attribute::resolveAttribute(value: $this->attribute_ref);
            $this->attribute_ref = $this->given_attribute->ref_uuid;
        }

        if (!$this->given_type) {
            $this->type_ref = static::stringFromCollection(collection: $col,param_name: 'type_ref');
            $this->given_type = ElementType::resolveType(value: $this->type_ref);
            $this->type_ref = $this->given_type->ref_uuid;
        }

        if (!$this->given_set) {
            $this->set_ref = static::stringFromCollection(collection: $col,param_name: 'set_ref');
            $this->given_set = ElementType::resolveType(value: $this->set_ref);
            $this->set_ref = $this->given_set->ref_uuid;
        }

        $this->is_set = static::boolFromCollection(collection: $col,param_name: 'is_set');


        if ($do_validation) {
            try {
                Validator::make(
                    [
                        'namespace_ref' => $this->given_namespace
                    ],
                    [
                        'namespace_ref' => ['nullable', new NamespaceMemberReq]
                    ]
                )->validate();

            } catch (ValidationException $v) {
                throw new HexbatchNotPossibleException($v->getMessage(),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::BAD_REGISTRATION);
            }
        }


    }

    public  function toArray() : array  {
        $what = parent::toArray();
        $what['namespace_ref'] = $this->namespace_ref;
        $what['shape_ref'] = $this->shape_ref;
        $what['schedule_ref'] = $this->schedule_ref;
        $what['attribute_ref'] = $this->attribute_ref;
        $what['type_ref'] = $this->type_ref;
        $what['set_ref'] = $this->set_ref;
        if ($this->is_set !== null) {
            $what['is_set'] = $this->is_set;
        }
        return $what;
    }



    public function getGivenNamespace(): ?UserNamespace
    {
        return $this->given_namespace;
    }

    public function getIsSet(): ?bool
    {
        return $this->is_set;
    }

    public function getGivenLocation(): ?LocationBound
    {
        return $this->given_location;
    }

    public function getGivenAttribute(): ?Attribute
    {
        return $this->given_attribute;
    }

    public function getGivenSchedule(): ?TimeBound
    {
        return $this->given_schedule;
    }

    public function getGivenType(): ?ElementType
    {
        return $this->given_type;
    }

    public function getGivenSet(): ?ElementSet
    {
        return $this->given_set;
    }

    public function setGivenType(?ElementType $given_type): ListElementParams
    {
        $this->given_type = $given_type;
        $this->type_ref = $this->given_type?->ref_uuid;
        return $this;
    }

    public function setGivenSet(?ElementSet $given_set): ListElementParams
    {
        $this->given_set = $given_set;
        $this->set_ref = $this->given_set?->ref_uuid;
        return $this;
    }

    public function getWorkingPhase(): ?Phase
    {
        return $this->working_phase;
    }





}
