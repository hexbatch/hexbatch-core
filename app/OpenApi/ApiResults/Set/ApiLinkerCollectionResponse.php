<?php

namespace App\OpenApi\ApiResults\Set;



use App\Models\Element;

use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;

use App\OpenApi\Results\Set\LinkerCollectionResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the link collection and thing for the api call
 */
#[OA\Schema(schema: 'ApiLinkerCollectionResponse')]
class ApiLinkerCollectionResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Links')]
    public LinkerCollectionResponse $link_collection;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
        Element $linker_element,
        bool $show_linker = false,
        bool $show_definer = false,
        bool $show_parent = false,bool $show_elements = false,bool $show_set = false,
        int $definer_type_level = 0,int $children_set_level = 0,int $parent_set_level = 0,
        int $type_level = 0,int $attribute_level = 0,int $namespace_level = 0, int $phase_level = 0,
        ?Thing $thing = null
    )
    {
        $this->link_collection = new LinkerCollectionResponse(linker_element: $linker_element,
            show_linker: $show_linker,show_definer: $show_definer,show_parent: $show_parent,show_elements: $show_elements ,show_set: $show_set,
            definer_type_level: $definer_type_level,children_set_level: $children_set_level,parent_set_level: $parent_set_level,
            type_level: $type_level,attribute_level: $attribute_level,namespace_level: $namespace_level,phase_level: $phase_level
           );

        if ($thing) {
            $this->thing = new ApiThingResponse(thing:$thing);
        }

    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['link_collection'] = $this->link_collection;
        if ($this->thing) {
            $ret['thing'] = $this->thing;
        }

        return $ret;
    }

}
