<?php

namespace App\OpenApi\Params\Actioning\Element;



use App\Models\Element;
use App\Models\ElementSet;
use App\OpenApi\ApiThingBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

/*
 * Create link params
 * element_uuid: required to create a set. An element can make one set. This can be uuid.
 * parent_set_uuid: A set can optionally have a parent set.  Parents cannot be changed later. Children can be parents. This can be uuid.
 * bool has_events: A set can choose to turn off events fired when an element enters or leaves it. Cannot be changed later.
 */
#[OA\Schema(schema: 'LinkCreateParams')]
class LinkCreateParams extends ApiThingBase
{

    #[OA\Property(title: 'Element',description: 'The anchor to the link')]
    protected ?string $element_ref = null;

    #[OA\Property(title: 'Set',description: 'The target')]
    protected ?string $target_set_ref = null;



    public function __construct(
        protected ?Element    $given_element = null,
        protected ?ElementSet $given_set = null

    )
    {
        parent::__construct();
        $this->element_ref = $this->given_element?->ref_uuid;
        $this->target_set_ref = $this->given_set?->ref_uuid;
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

        if (!$this->given_set) {
            if ($col->has('target_set_ref') && $col->get('target_set_ref')) {
                $this->given_set = ElementSet::resolveSet(value: $col->get('target_set_ref'));
                $this->target_set_ref = $this->given_set->ref_uuid;
            }
        }

    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['element_ref'] = $this->element_ref;
        $ret['target_set_ref'] = $this->target_set_ref;

        return $ret;
    }

    public function getElementRef(): ?string
    {
        return $this->element_ref;
    }

    public function getTargetSetRef(): ?string
    {
        return $this->target_set_ref;
    }



}
