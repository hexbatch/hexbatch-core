<?php

namespace App\OpenApi\Results\Attributes;

use App\Models\Attribute;
use App\OpenApi\Results\ResultCursorBase;
use Illuminate\Pagination\AbstractCursorPaginator;
use OpenApi\Attributes as OA;

/**
 * A collection of time bounds
 */
#[OA\Schema(schema: 'AttributeCollectionResponse',title: "Types")]
class AttributeCollectionResponse extends ResultCursorBase
{

    #[OA\Property( title: 'List of Attributes')]
    /**
     * @var AttributeResponse[] $attributes
     */
    public array $attributes = [];

    /**
     * @param Attribute[]|AbstractCursorPaginator $given_types
     */
    public function __construct($given_types, int $attribute_levels = 0, int $owning_type_levels = 0, int $design_levels = 0)
    {
        parent::__construct($given_types);
        $this->attributes = [];
        foreach ($given_types as $attr) {
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
