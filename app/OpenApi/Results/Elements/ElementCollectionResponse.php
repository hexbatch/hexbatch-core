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
    public function __construct($given_elements)
    {
        parent::__construct($given_elements);
        $this->elements = [];
        foreach ($given_elements as $ele) {
            $this->elements[] = new ElementResponse(given_element: $ele);
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['elements'] = $this->elements;
        return $ret;
    }


}
