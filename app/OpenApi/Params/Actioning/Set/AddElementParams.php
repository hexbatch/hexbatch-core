<?php

namespace App\OpenApi\Params\Actioning\Set;



use App\Helpers\Utilities;
use App\Models\Element;
use App\Models\ElementSet;
use App\OpenApi\ApiThingBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

/*
 * Add element params
  given_set_uuid: uuid of the set
  given_element_uuids: array of one or more element uuids to put into the set
  is_sticky: if the elements are sticky, remaining after the remove command
 */
#[OA\Schema(schema: 'AddElementParams')]
class AddElementParams extends ApiThingBase
{

    #[OA\Property(title: 'Set',description: 'The set which will have the elements. This is uuid')]
    protected ?string $set_ref = null;

    #[OA\Property(title: 'Elements',description: 'The elements to add to the set. This is uuid ')]
    /** @var string[] $element_refs */
    protected array $element_refs = [];

    #[OA\Property(title: 'Has events',description: 'A set can choose to turn off events fired when an element enters or leaves it. Cannot be changed later.')]
    protected bool $is_sticky = true;


    public function __construct(
        protected ?ElementSet       $given_set = null,
        /** @var Element[] $elements */
        protected array $elements = []

    )
    {
        parent::__construct();
        $this->set_ref = $this->given_set?->ref_uuid;
        if (count($this->elements)) {
            foreach ($this->elements as $ele) {
                $this->element_refs[] = $ele->ref_uuid;
            }
        }

    }


    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);


        if (!$this->given_set) {
            if ($col->has('set_ref') && $col->get('set_ref')) {
                $this->given_set = ElementSet::resolveSet(value: $col->get('set_ref'));
                $this->set_ref = $this->given_set->ref_uuid;
            }
        }

        if (!count($this->elements) ) {
            if ($col->has('element_refs') && is_array($col->get('element_refs')) ) {
                $this->elements = Element::resolveElements(values: $col->get('element_refs'));
                foreach ($this->elements as $ele) {
                    $this->element_refs[] = $ele->ref_uuid;
                }
            }
        }


        if ($col->has('is_sticky')) {
            $this->is_sticky = Utilities::boolishToBool($col->get('is_sticky'));
        }

    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['set_ref'] = $this->set_ref;
        $ret['element_refs'] = $this->element_refs;
        $ret['is_sticky'] = $this->is_sticky;

        return $ret;
    }

    public function getSetRef(): ?string
    {
        return $this->set_ref;
    }

    public function getElementRefs(): array
    {
        return $this->element_refs;
    }

    public function isSticky(): bool
    {
        return $this->is_sticky;
    }






}
