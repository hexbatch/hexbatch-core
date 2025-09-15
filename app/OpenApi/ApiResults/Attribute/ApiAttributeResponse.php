<?php

namespace App\OpenApi\ApiResults\Attribute;

use App\Models\Attribute;
use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ThingResponse;
use App\OpenApi\Results\Attributes\AttributeResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the attribute and thing for the api call
 */
#[OA\Schema(schema: 'ApiAttributeResponse')]
class ApiAttributeResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Attribute')]
    public AttributeResponse $attribute;

    #[OA\Property(title: 'Thing')]
    public ?ThingResponse $thing = null;


    public function __construct(
         Attribute $given_attribute,
         int $attribute_levels = 0,int $owning_type_levels = 0,int $design_levels = 0,
         ?Thing $thing = null
    )
    {
        $this->attribute = new AttributeResponse(given_attribute: $given_attribute,
            attribute_levels: $attribute_levels,owning_type_levels: $owning_type_levels,design_levels: $design_levels);

        $this->thing = new ThingResponse(thing:$thing);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['attribute'] = $this->attribute;
        $ret['thing'] = $this->thing;
        return $ret;
    }

}
