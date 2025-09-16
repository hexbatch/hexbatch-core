<?php

namespace App\OpenApi\ApiResults\Attribute;


use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;
use App\OpenApi\Results\Attributes\AttributeCollectionResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the attribute list and thing for the api call
 */
#[OA\Schema(schema: 'ApiAttributeCollectionResponse')]
class ApiAttributeCollectionResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Attribute collection')]
    public AttributeCollectionResponse $attribute_collection;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct($given_attributes,  int $attribute_levels = 0,int $owning_type_levels = 0,int $design_levels = 0,?Thing $thing = null)
    {
        $this->attribute_collection = new AttributeCollectionResponse(given_types: $given_attributes,
            attribute_levels: $attribute_levels,owning_type_levels: $owning_type_levels,design_levels: $design_levels);

        $this->thing = new ApiThingResponse(thing:$thing);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['attributes'] = $this->attribute_collection;
        $ret['thing'] = $this->thing;
        return $ret;
    }

}
