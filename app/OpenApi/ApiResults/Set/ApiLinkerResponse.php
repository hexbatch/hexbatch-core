<?php

namespace App\OpenApi\ApiResults\Set;




use App\Models\ElementLink;
use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;

use App\OpenApi\Results\Set\LinkResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the link and thing for the api call
 */
#[OA\Schema(schema: 'ApiLinkerResponse')]
class ApiLinkerResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Link')]
    public LinkResponse $link;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
        ElementLink $linker,
        bool $show_linker = false,
        bool $show_definer = false,
        bool $show_parent = false,bool $show_elements = false,bool $show_set = false,
        int $definer_type_level = 0,int $children_set_level = 0,int $parent_set_level = 0,
        ?Thing $thing = null
    )
    {
        $this->link = new LinkResponse(linker: $linker,
            show_linker: $show_linker, show_set: $show_set, show_elements: $show_elements, show_definer: $show_definer, show_parent: $show_parent,
            definer_type_level: $definer_type_level, children_set_level: $children_set_level, parent_set_level: $parent_set_level
           );

        if ($thing) {
            $this->thing = new ApiThingResponse(thing:$thing);
        }

    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['link'] = $this->link;
        if ($this->thing) {
            $ret['thing'] = $this->thing;
        }

        return $ret;
    }

}
