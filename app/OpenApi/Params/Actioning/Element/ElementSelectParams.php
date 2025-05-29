<?php

namespace App\OpenApi\Params\Actioning\Element;




use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\OpenApi\ApiCallBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ElementSelectParams')]
class ElementSelectParams extends ApiCallBase
{

    #[OA\Property(title: 'Elements',description: 'The elements to select. ')]
    /** @var string[] $element_refs */
    protected array $element_refs = [];


    #[OA\Property(title: 'Type',description: 'The element is made from this type. Can be uuid or name')]
    protected ?string $type_ref = null;

    #[OA\Property(title: 'Set',description: 'The set which will have the elements. This is uuid')]
    protected ?string $set_ref = null;

    #[OA\Property(title: 'Phase',description: 'The phase to restrict this selection. This can be a uuid or name')]
    protected ?string $phase_ref = null;

    #[OA\Property(title: 'Attribute',description: 'The attribute to read or write or do actions. This can be a uuid or name')]
    protected ?string $attribute_ref = null;


    public function __construct(
        /** @var Element[] $elements */
        protected array $elements = [],
        protected ?Phase $given_phase = null,
        protected ?ElementSet $given_set = null,
        protected ?ElementType $given_type = null,
        protected ?Attribute $given_attribute = null,

    )
    {
        parent::__construct();
        $this->phase_ref = $this->given_phase?->ref_uuid;
        $this->type_ref = $this->given_type?->ref_uuid;
        $this->set_ref = $this->given_set?->ref_uuid;
        $this->attribute_ref = $this->given_attribute?->ref_uuid;
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

        if (!$this->given_phase) {
            if ($col->has('phase_ref') && $col->get('phase_ref')) {
                $this->given_phase = Phase::resolvePhase(value: $col->get('phase_ref'));
                $this->phase_ref = $this->given_phase->ref_uuid;
            }
        }

        if (!$this->given_type) {
            if ($col->has('type_ref') && $col->get('type_ref')) {
                $this->given_type = ElementType::resolveType(value: $col->get('type_ref'));
                $this->type_ref = $this->given_type->ref_uuid;
            }
        }

        if (!$this->given_attribute) {
            if ($col->has('attribute_ref') && $col->get('attribute_ref')) {
                $this->given_attribute = Attribute::resolveAttribute(value: $col->get('attribute_ref'));
                $this->attribute_ref = $col->get('attribute_ref');
            }
        }


        if (!count($this->elements) ) {
            if ($col->has('element_refs') && is_array($col->get('element_refs')) ) {
                $this->elements = Element::resolveElements(values: $col->get('element_refs'));
                foreach ($this->elements as $ele) {
                    if ($this->given_phase && ($ele->element_phase_id !== $this->given_phase->id ) ) {
                       continue;
                    }
                    $this->element_refs[] = $ele->ref_uuid;
                }
            }
        }

    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['element_refs'] = $this->element_refs;
        $ret['type_ref'] = $this->type_ref;
        $ret['set_ref'] = $this->set_ref;
        $ret['phase_ref'] = $this->phase_ref;
        $ret['attribute_ref'] = $this->attribute_ref;

        return $ret;
    }

    public function getElementRefs(): array
    {
        return $this->element_refs;
    }

    public function getFirstElementRef(): ?string
    {
        return $this->element_refs[0]??null;
    }

    public function getTypeRef(): ?string
    {
        return $this->type_ref;
    }

    public function getSetRef(): ?string
    {
        return $this->set_ref;
    }

    public function getPhaseRef(): ?string
    {
        return $this->phase_ref;
    }

    public function getAttributeRef(): ?string
    {
        return $this->attribute_ref;
    }

}
