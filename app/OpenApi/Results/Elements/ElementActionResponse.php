<?php

namespace App\OpenApi\Results\Elements;


use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\OpenApi\Results\Attributes\AttributeResponse;
use App\OpenApi\Results\Phase\PhaseResponse;
use App\OpenApi\Results\ResultBase;
use App\OpenApi\Results\Set\SetResponse;
use App\OpenApi\Results\Types\TypeResponse;
use OpenApi\Attributes as OA;


/**
 * Show details about an element
 */
#[OA\Schema(schema: 'ElementActionResponse')]
class ElementActionResponse extends ResultBase
{




    public function __construct(
        #[OA\Property(title: 'Element')]
        public ?ElementResponse $element = null  ,

        #[OA\Property(title: 'Set')]
        public ?SetResponse $set  = null,

        #[OA\Property(title: 'Type')]
        public ?TypeResponse $type = null,

        #[OA\Property(title: 'Attribute')]
        public ?AttributeResponse $attribute = null,

        #[OA\Property(title: 'Phase')]
        public ?PhaseResponse $phase = null,

        #[OA\Property(title: 'Value', items: new OA\Items(), nullable: true)]
        /** @var mixed[] $value */
        public array $value = [],

        protected ?Element $given_element = null,
        protected ?ElementSet $given_set = null,
        protected ?ElementType $given_type = null,
        protected ?Attribute $given_attribute = null,
        protected ?Phase $given_phase = null,
    )
    {
        if ($this->given_element) {
            $this->element = new ElementResponse(given_element: $this->given_element);
        }

        if ($this->given_set) {
            $this->set = new SetResponse(given_set: $this->given_set);
        }

        if ($this->given_type) {
            $this->type = new TypeResponse(given_type: $this->given_type);
        }

        if ($this->given_attribute) {
            $this->attribute = new AttributeResponse(given_attribute: $this->given_attribute);
        }

        if ($this->given_phase) {
            $this->phase = new PhaseResponse(given_phase: $this->given_phase);
        }

    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['element'] = $this->element;
        $ret['set'] = $this->set;
        $ret['type'] = $this->type;
        $ret['attribute'] = $this->attribute;
        $ret['phase'] = $this->phase;
        $ret['value'] = $this->value;

        return $ret;
    }

}
