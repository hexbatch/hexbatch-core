<?php

namespace App\OpenApi\Results\Attributes;

use App\Models\Attribute;
use App\OpenApi\Results\ResultThingBase;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\OpenApi\Things\ThingMimimalResponseTrait;
use Illuminate\Pagination\AbstractCursorPaginator;
use OpenApi\Attributes as OA;

/**
 * A collection of time bounds
 */
#[OA\Schema(schema: 'AttributeCollectionResponse',title: "Types")]
class AttributeCollectionResponse extends ResultThingBase
{

    use ThingMimimalResponseTrait;
    #[OA\Property( title: 'List of Attributes')]
    /**
     * @var AttributeResponse[] $attributes
     */
    public array $attributes = [];

    /**
     * @param Attribute[]|AbstractCursorPaginator $given_attributes
     */
    public function __construct($given_attributes,  int $attribute_levels = 0,int $owning_type_levels = 0,int $design_levels = 0,?Thing $thing = null)
    {
        parent::__construct($given_attributes,$thing);
        $this->attributes = [];
        foreach ($given_attributes as $attr) {
            $this->attributes[] = new AttributeResponse(given_attribute: $attr,
                attribute_levels: $attribute_levels,
                owning_type_levels: $owning_type_levels,
                design_levels: $design_levels,
            );
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();

        $ret['attributes'] = $this->attributes;
        return $ret;
    }


}
