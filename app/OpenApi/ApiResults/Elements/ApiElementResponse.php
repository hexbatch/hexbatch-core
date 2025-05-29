<?php

namespace App\OpenApi\ApiResults\Elements;


use App\Models\Element;
use App\Models\ElementSetMember;
use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;
use App\OpenApi\Results\Elements\ElementResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the element details and thing for the api call
 */
#[OA\Schema(schema: 'ApiElementResponse')]
class ApiElementResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Element')]
    public ElementResponse $element;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
        Element $given_element,?ElementSetMember $member = null
        ,int $type_level = 0,int $attribute_level = 0,int $namespace_level = 0, int $phase_level = 0,
         ?Thing $thing = null
    )
    {
        $this->element = new ElementResponse(given_element: $given_element,member: $member,type_level: $type_level,
        attribute_level: $attribute_level,namespace_level: $namespace_level,phase_level: $phase_level);

        $this->thing = new ApiThingResponse(thing:$thing);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['element'] = $this->element;
        $ret['thing'] = $this->thing;
        return $ret;
    }

}
