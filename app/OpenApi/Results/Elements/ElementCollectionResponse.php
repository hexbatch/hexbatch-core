<?php

namespace App\OpenApi\Results\Elements;

use App\Models\Element;
use App\OpenApi\Results\ResultThingBase;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\OpenApi\Things\ThingMimimalResponseTrait;
use OpenApi\Attributes as OA;

/**
 * A collection of elements
 */
#[OA\Schema(schema: 'ElementCollectionResponse')]
class ElementCollectionResponse extends ResultThingBase
{

    use ThingMimimalResponseTrait;


    #[OA\Property( title: 'List of elements')]
    /**
     * @var ElementResponse[] $elements
     */
    public array $elements = [];

    /**
     * @param Element[] $given_elements
     */
    public function __construct($given_elements,int $type_level = 0,int $attribute_level = 0,int $namespace_level = 0, int $phase_level = 0,?Thing $thing = null)
    {
        parent::__construct($given_elements,$thing);
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
