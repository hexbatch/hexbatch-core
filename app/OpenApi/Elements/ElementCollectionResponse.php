<?php

namespace App\OpenApi\Elements;

use App\Models\Element;
use JsonSerializable;
use OpenApi\Attributes as OA;

/**
 * A collection of elements
 */
#[OA\Schema(schema: 'ElementCollectionResponse',title: "Hooks")]
class ElementCollectionResponse implements  JsonSerializable
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
        $this->elements = [];
        foreach ($given_elements as $ele) {
            $this->elements[] = new ElementResponse(given_element: $ele);
        }

    }

    public function jsonSerialize(): array
    {
        $arr = [];

        $arr['elements'] = $this->elements;
        return $arr;
    }


}
