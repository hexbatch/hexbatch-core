<?php

namespace App\OpenApi\ApiResults\Set;


use App\Models\ElementSet;
use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ThingResponse;
use App\OpenApi\Results\Set\SetResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the set detail and thing for the api call
 */
#[OA\Schema(schema: 'ApiSetResponse')]
class ApiSetResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Set')]
    public SetResponse $set;

    #[OA\Property(title: 'Thing')]
    public ?ThingResponse $thing = null;


    public function __construct(
        ElementSet $given_set,bool $show_definer = false,
        bool $show_parent = false,bool $show_elements = false,
        int $definer_type_level = 0,int $children_set_level = 0,int $parent_set_level = 0,
         ?Thing $thing = null
    )
    {
        $this->set = new SetResponse(given_set: $given_set,show_definer: $show_definer,show_parent: $show_parent,
                                                show_elements: $show_elements,
                                                definer_type_level: $definer_type_level,children_set_level: $children_set_level,
                                                parent_set_level: $parent_set_level);

        if ($thing) {
            $this->thing = new ThingResponse(thing:$thing);
        }
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['set'] = $this->set;
        if ($this->thing) {
            $ret['thing'] = $this->thing;
        }
        return $ret;
    }

}
