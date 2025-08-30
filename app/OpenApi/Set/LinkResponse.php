<?php

namespace App\OpenApi\Set;

use App\OpenApi\Common\HexbatchUuid;


use App\Models\ElementLink;
use App\OpenApi\Elements\ElementResponse;

use Carbon\Carbon;
use JsonSerializable;
use OpenApi\Attributes as OA;


/**
 * Show details about the sets linked together by an element
 */
#[OA\Schema(schema: 'LinkerCollectionResponse')]
class LinkResponse implements  JsonSerializable
{
    #[OA\Property(title: 'Link uuid',type: HexbatchUuid::class)]
    public string $uuid = '';

    #[OA\Property(title: 'Linking element uuid',type: HexbatchUuid::class)]
    public string $linking_element_uuid = '';


    #[OA\Property(title: 'Linking set uuid',type: HexbatchUuid::class)]
    public string $linked_set_uuid = '';




    #[OA\Property(title: 'Linking Element')]
    public ?ElementResponse $linking_element = null  ;

    #[OA\Property(title: 'Linked Set')]
    public ?SetResponse $linked_set = null  ;

    #[OA\Property(title: 'Namespace created at',format: 'date-time')]
    public ?string $created_at = '';


    public function __construct(ElementLink $linker,bool $show_linker = false,
                                bool        $show_set = false,bool $show_elements = false,
                                bool        $show_definer = false, bool $show_parent = false,
                                int         $definer_type_level = 0,int $children_set_level = 0,int $parent_set_level = 0)
    {
        $this->uuid = $linker->ref_uuid;
        $this->linking_element_uuid = $linker->linking_element->ref_uuid;
        $this->linked_set_uuid = $linker->linked_set->ref_uuid;
        if ($show_linker) {
            $this->linking_element = new ElementResponse(given_element: $linker->linking_element,type_level: $definer_type_level);
        }

        if ($show_set) {
            $this->linked_set = new SetResponse(given_set: $linker->linked_set,
                show_definer: $show_definer,show_parent: $show_parent,show_elements: $show_elements,definer_type_level: $definer_type_level,
                children_set_level: $children_set_level,parent_set_level: $parent_set_level
            );
        }

        $this->created_at = $linker->created_at? Carbon::parse($linker->created_at,'UTC')
            ->timezone(config('app.timezone'))->toIso8601String():null;


    }


    public function jsonSerialize(): array
    {
        $ret = [];
        $ret['uuid'] = $this->uuid;
        $ret['linking_element_uuid'] = $this->linking_element_uuid;
        $ret['linked_set_uuid'] = $this->linked_set_uuid;
        $ret['created_at'] = $this->created_at;

        if ($this->linking_element) {
            $ret['defining_element'] = $this->linking_element;
        }

        if ($this->linking_element) {
            $ret['linking_element'] = $this->linking_element;
        }

        if ($this->linked_set ) {
            $ret['linked_set'] = $this->linked_set;
        }

        return $ret;
    }

}
