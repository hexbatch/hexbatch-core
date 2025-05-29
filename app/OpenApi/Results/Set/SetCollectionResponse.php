<?php

namespace App\OpenApi\Results\Set;


use App\Models\ElementSet;
use App\OpenApi\Results\ResultCursorBase;
use OpenApi\Attributes as OA;

/**
 * A collection of elements
 */
#[OA\Schema(schema: 'SetCollectionResponse',title: "Sets")]
class SetCollectionResponse extends ResultCursorBase
{



    #[OA\Property( title: 'List of elements')]
    /**
     * @var SetResponse[] $sets
     */
    public array $sets = [];

    /**
     * @param ElementSet[] $given_sets
     */
    public function __construct($given_sets,bool $show_definer = false,
                                bool $show_parent = false,bool $show_elements = false,
                                int $definer_type_level = 0,int $children_set_level = 0,int $parent_set_level = 0)
    {
        parent::__construct($given_sets);
        $this->sets = [];
        foreach ($given_sets as $set) {
            $this->sets[] = new SetResponse(given_set: $set,show_definer: $show_definer,show_parent: $show_parent,show_elements: $show_elements,
            definer_type_level: $definer_type_level,children_set_level: $children_set_level,parent_set_level: $parent_set_level);
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();

        $ret['sets'] = $this->sets;
        return $ret;
    }


}
