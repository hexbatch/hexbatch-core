<?php

namespace App\OpenApi\ApiResults\Type;


use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;

use App\OpenApi\Results\Types\TypeCollectionResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the type list and thing for the api call
 */
#[OA\Schema(schema: 'ApiTypeCollectionResponse')]
class ApiTypeCollectionResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Types')]
    public TypeCollectionResponse $types;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
        $given_types, int $namespace_levels = 0, int $parent_levels = 0,
        int $attribute_levels = 0, int $inherited_attribute_levels = 0,
        int $number_time_spans = 1,
         ?Thing $thing = null
    )
    {
        $this->types = new TypeCollectionResponse(given_types: $given_types,namespace_levels: $namespace_levels,parent_levels: $parent_levels,
                                                attribute_levels: $attribute_levels,
                                                inherited_attribute_levels: $inherited_attribute_levels,number_time_spans: $number_time_spans);

        if ($thing) {
            $this->thing = new ApiThingResponse(thing:$thing);
        }
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['types'] = $this->types;
        if ($this->thing) {
            $ret['thing'] = $this->thing;
        }
        return $ret;
    }

}
