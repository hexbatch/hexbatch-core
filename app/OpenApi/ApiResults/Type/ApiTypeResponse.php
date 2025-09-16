<?php

namespace App\OpenApi\ApiResults\Type;


use App\Models\ElementType;
use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;

use App\OpenApi\Results\Types\TypeResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the type detail and thing for the api call
 */
#[OA\Schema(schema: 'ApiTypeResponse')]
class ApiTypeResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Type')]
    public TypeResponse $type;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
        ElementType $given_type,int $namespace_levels = 0,int $parent_levels = 0,
        int $attribute_levels = 0, int $inherited_attribute_levels = 0,
                    $number_time_spans = 1,
         ?Thing $thing = null
    )
    {
        $this->type = new TypeResponse(given_type: $given_type,namespace_levels: $namespace_levels,parent_levels: $parent_levels,
                                        attribute_levels: $attribute_levels,
                                        inherited_attribute_levels: $inherited_attribute_levels,number_time_spans: $number_time_spans);

        if ($thing) {
            $this->thing = new ApiThingResponse(thing:$thing);
        }
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['type'] = $this->type;
        if ($this->thing) {
            $ret['thing'] = $this->thing;
        }
        return $ret;
    }

}
