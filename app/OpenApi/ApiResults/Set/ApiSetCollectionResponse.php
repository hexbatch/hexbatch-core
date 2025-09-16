<?php

namespace App\OpenApi\ApiResults\Set;


use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;
use App\OpenApi\Results\Set\SetCollectionResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the set list and thing for the api call
 */
#[OA\Schema(schema: 'ApiSetCollectionResponse')]
class ApiSetCollectionResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Sets')]
    public SetCollectionResponse $sets;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
        $given_sets,bool $show_definer = false,
        bool $show_parent = false,bool $show_elements = false,
        int $definer_type_level = 0,int $children_set_level = 0,int $parent_set_level = 0,
         ?Thing $thing = null
    )
    {
        $this->sets = new SetCollectionResponse(given_sets: $given_sets,show_definer: $show_definer,show_parent: $show_parent,
                                                show_elements: $show_elements,
                                                definer_type_level: $definer_type_level,children_set_level: $children_set_level,
                                                parent_set_level: $parent_set_level);

        if ($thing) {
            $this->thing = new ApiThingResponse(thing:$thing);
        }
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['sets'] = $this->sets;
        if ($this->thing) {
            $ret['thing'] = $this->thing;
        }
        return $ret;
    }

}
