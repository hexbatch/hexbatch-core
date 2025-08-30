<?php

namespace App\OpenApi\Set;

use App\OpenApi\Common\HexbatchUuid;

use App\Models\Element;
use App\Models\ElementLink;
use App\Models\ElementSet;

use App\OpenApi\Elements\ElementResponse;


use JsonSerializable;
use OpenApi\Attributes as OA;


/**
 * Show details about the sets linked together by an element
 */
#[OA\Schema(schema: 'LinkerCollectionResponse')]
class LinkerCollectionResponse implements  JsonSerializable
{


    #[OA\Property(title: 'Linking element uuid',type: HexbatchUuid::class)]
    public string $linking_element_uuid = '';



    #[OA\Property(title: 'Linking Element')]
    public ?ElementResponse $linking_element  = null ;


    #[OA\Property( title: 'List of links')]
    /**
     * @var LinkResponse[] $links
     */
    public array $links = [];


    public function __construct(Element $linker_element,
                                bool $show_linker = false,
                                bool $show_definer = false,
                                bool $show_parent = false,bool $show_elements = false,bool $show_set = false,
                                int $definer_type_level = 0,int $children_set_level = 0,int $parent_set_level = 0,
         int $type_level = 0,int $attribute_level = 0,int $namespace_level = 0, int $phase_level = 0
    )
    {
        $this->linking_element_uuid = $linker_element->ref_uuid;
        if ($show_linker) {
            $this->linking_element = new ElementResponse(given_element: $linker_element,
                type_level: $type_level, attribute_level: $attribute_level,
                namespace_level: $namespace_level, phase_level: $phase_level);
        }

        /** @var ElementSet[] $sets */
        $links = ElementLink::buildLink(linking_element_id: $linker_element->id, with_linker_element: true, with_linked_set: true)->get();
        foreach ($links as $link) {
            $this->links[] = new LinkResponse(linker: $link, show_linker: $show_linker, show_set: $show_set,
                show_elements: $show_elements, show_definer: $show_definer, show_parent: $show_parent, definer_type_level: $definer_type_level,
                children_set_level: $children_set_level, parent_set_level: $parent_set_level);
        }


    }


    public function jsonSerialize(): array
    {
        $ret = [];
        $ret['linking_element_uuid'] = $this->linking_element_uuid;

        if ($this->linking_element) {
            $ret['defining_element'] = $this->linking_element;
        }

        if ($this->linking_element) {
            $ret['linking_element'] = $this->linking_element;
        }

        if (count($this->links )) {
            $ret['links'] = $this->links;
        }


        return $ret;
    }

}
