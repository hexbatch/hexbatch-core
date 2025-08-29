<?php

namespace App\OpenApi\Params\Set;



use App\Helpers\Utilities;

use App\Models\Element;
use App\Models\ElementSet;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Api\ApiParamBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

/**
 * Create set params
 * element_uuid: required to create a set. An element can make one set. This can be uuid.
 * parent_set_uuid: A set can optionally have a parent set.  Parents cannot be changed later. Children can be parents. This can be uuid.
 * bool has_events: A set can choose to turn off events fired when an element enters or leaves it. Cannot be changed later.
 */
#[OA\Schema(schema: 'SetCreateParams')]
class SetCreateParams extends ApiParamBase
{

    #[OA\Property(title: 'Element',description: 'required to create a set. An element can make one set')]
    protected ?string $element_ref = null;

    #[OA\Property(title: 'Parent',description: 'A set can optionally have a parent set.  Parents cannot be changed later. Children can be parents. ')]
    protected ?string $parent_set_ref = null;

    #[OA\Property(title: 'Has events',description: 'A set can choose to turn off events fired when an element enters or leaves it. Cannot be changed later.')]
    protected bool $has_events = true;


    public function __construct(
        protected ?Element       $given_element = null,
        protected ?ElementSet       $given_parent = null,
        protected ?UserNamespace $namespace = null,

    )
    {
        $this->element_ref = $this->given_element?->ref_uuid;
        $this->parent_set_ref = $this->given_parent?->ref_uuid;


    }


    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);


        if (!$this->given_element) {
            if ($col->has('element_ref') && $col->get('element_ref')) {
                $this->given_element = Element::resolveElement(value: $col->get('element_ref'));
                $this->element_ref = $this->given_element->ref_uuid;
            }
        }

        if (!$this->given_parent) {
            if ($col->has('parent_set_ref') && $col->get('parent_set_ref')) {
                $this->given_parent = ElementSet::resolveSet(value: $col->get('parent_set_ref'));
                $this->parent_set_ref = $this->given_parent->ref_uuid;
            }
        }


        if ($col->has('has_events')) {
            $this->has_events = Utilities::boolishToBool($col->get('has_events'));
        }

    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['element_ref'] = $this->element_ref;
        $ret['parent_set_ref'] = $this->parent_set_ref;
        $ret['has_events'] = $this->has_events;

        return $ret;
    }

    public function getElementRef(): ?string
    {
        return $this->element_ref;
    }

    public function getParentSetRef(): ?string
    {
        return $this->parent_set_ref;
    }

    public function hasEvents(): bool
    {
        return $this->has_events;
    }





}
