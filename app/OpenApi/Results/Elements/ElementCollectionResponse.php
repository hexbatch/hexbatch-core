<?php

namespace App\OpenApi\Results\Elements;

use App\Models\Element;
use App\OpenApi\Results\ResultDataBase;
use OpenApi\Attributes as OA;

/**
 * A collection of elements
 */
#[OA\Schema(schema: 'ElementCollectionResponse')]
class ElementCollectionResponse extends ResultDataBase
{




    #[OA\Property( title: 'List of elements')]
    /**
     * @var ElementResponse[] $elements
     */
    public array $elements = [];

    /**
     * @param Element[] $given_elements
     */
    public function __construct($given_elements,int $type_level = 0,int $attribute_level = 0,int $namespace_level = 0, int $phase_level = 0)
    {
        parent::__construct($given_elements);
        $this->elements = [];
        foreach ($given_elements as $ele) {
            $this->elements[] = new ElementResponse(
                given_element: $ele,
                type_level: $type_level,
                attribute_level: $attribute_level,
                namespace_level: $namespace_level,
                phase_level: $phase_level
            );
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['elements'] = $this->elements;
        return $ret;
    }


}
