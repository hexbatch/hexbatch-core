<?php

namespace App\OpenApi\ApiResults\Elements;


use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;
use App\OpenApi\Results\Elements\ElementCollectionResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the element details and thing for the api call
 */
#[OA\Schema(schema: 'ApiElementCollectionResponse')]
class ApiElementCollectionResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Elements')]
    public ElementCollectionResponse $element_collection;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
         $given_elements,
         int $type_level = 0,int $attribute_level = 0,int $namespace_level = 0, int $phase_level = 0,
         ?Thing $thing = null
    )
    {
        $this->element_collection = new ElementCollectionResponse(given_elements: $given_elements,type_level: $type_level,attribute_level: $attribute_level,
        namespace_level: $namespace_level,phase_level: $phase_level);

        $this->thing = new ApiThingResponse(thing:$thing);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['element_collection'] = $this->element_collection;
        $ret['thing'] = $this->thing;
        return $ret;
    }

}
