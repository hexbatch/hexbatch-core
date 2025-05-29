<?php

namespace App\OpenApi\ApiResults\Elements;

use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;
use App\OpenApi\Results\Elements\ElementActionResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the element details and thing for the api call
 */
#[OA\Schema(schema: 'ApiElementActionResponse')]
class ApiElementActionResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Action')]
    public ElementActionResponse $action;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
         array $value = [],
         ?Element $given_element = null,
         ?ElementSet $given_set = null,
         ?ElementType $given_type = null,
         ?Attribute $given_attribute = null,
         ?Phase $given_phase = null,
         ?Thing $thing = null
    )
    {
        $this->action = new ElementActionResponse(value: $value,given_element: $given_element,given_set: $given_set,
        given_type: $given_type,given_attribute: $given_attribute,given_phase: $given_phase);

        $this->thing = new ApiThingResponse(thing:$thing);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['action'] = $this->action;
        $ret['thing'] = $this->thing;
        return $ret;
    }

}
