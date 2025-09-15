<?php

namespace App\OpenApi\Results\Types;

use App\Models\ElementType;
use App\OpenApi\Results\ResultCursorBase;
use Illuminate\Pagination\AbstractCursorPaginator;
use OpenApi\Attributes as OA;

/**
 * A collection of time bounds
 */
#[OA\Schema(schema: 'TypeCollectionResponse',title: "Types")]
class TypeCollectionResponse extends ResultCursorBase
{


    #[OA\Property( title: 'List of types')]
    /**
     * @var TypeResponse[] $types
     */
    public array $types = [];

    /**
     * @param ElementType[]|AbstractCursorPaginator $given_types
     */
    public function __construct($given_types, int $namespace_levels = 0, int $parent_levels = 0,
                                int $attribute_levels = 0, int $inherited_attribute_levels = 0,
                                int $number_time_spans = 1)
    {
        parent::__construct($given_types);
        $this->types = [];
        foreach ($given_types as $a_type) {
            $this->types[] = new TypeResponse(given_type: $a_type,
                namespace_levels: $namespace_levels,
                parent_levels: $parent_levels,
                attribute_levels: $attribute_levels,
                inherited_attribute_levels: $inherited_attribute_levels,
                number_time_spans: $number_time_spans,
            );
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();

        $ret['types'] = $this->types;
        return $ret;
    }


}
