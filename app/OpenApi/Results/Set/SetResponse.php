<?php

namespace App\OpenApi\Results\Set;

use App\Models\Element;
use App\Models\ElementSet;
use App\OpenApi\Common\HexbatchUuid;
use App\OpenApi\Results\Elements\ElementResponse;
use App\OpenApi\Results\ResultBase;
use Carbon\Carbon;
use OpenApi\Attributes as OA;


/**
 * Show details about a set
 */
#[OA\Schema(schema: 'SetResponse')]
class SetResponse extends ResultBase
{

    #[OA\Property(title: 'Set uuid',type: HexbatchUuid::class)]
    public string $uuid = '';

    #[OA\Property(title: 'Defining element uuid',type: HexbatchUuid::class)]
    public string $defining_element_uuid = '';


    #[OA\Property(title: 'Has events')]
    public bool $has_events  ;

    #[OA\Property(title: 'Defining Element')]
    public ?ElementResponse $defining_element = null  ;


    #[OA\Property(title: 'Parent Set')]
    public ?SetResponse $parent_set = null  ;

    #[OA\Property(title: 'Children sets')]
    /** @var SetResponse[] $children_sets */
    public array $children_sets = []  ;

    #[OA\Property(title: 'Children sets')]
    /** @var Element[] $member_elements */
    public array $member_elements = []  ;



    #[OA\Property(title: 'Namespace created at',format: 'date-time')]
    public ?string $created_at = '';






    public function __construct(ElementSet $given_set,bool $show_definer = false,
                                bool $show_parent = false,bool $show_elements = false,
                                int $definer_type_level = 0,int $children_set_level = 0,int $parent_set_level = 0)
    {
        parent::__construct();
        $this->uuid = $given_set->ref_uuid;
        $this->has_events = $given_set->has_events;
        if ($show_definer) {
            $this->defining_element = new ElementResponse(given_element: $given_set->defining_element,type_level: $definer_type_level);
        }
        $this->defining_element_uuid = $given_set->defining_element->ref_uuid;

        if ($show_parent) {
            /** @uses ElementSet::parent_set() */
            $this->parent_set = new SetResponse($given_set->parent_set,parent_set_level: $parent_set_level);
        }
        $this->created_at = $given_set->created_at? Carbon::parse($given_set->created_at,'UTC')
            ->timezone(config('app.timezone'))->toIso8601String():null;

        if ($children_set_level > 0 ) {
            /** @uses ElementSet::children_sets() */
            foreach ($given_set->children_sets as $child) {
                $this->children_sets[] = new SetResponse(given_set: $child,children_set_level: $children_set_level - 1);
            }
        }

        if ($show_elements) {
            foreach ($given_set->element_members as $member) {
                $this->member_elements[] = new ElementResponse(given_element: $member->of_element);
            }
        }



    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['uuid'] = $this->uuid;
        $ret['defining_element_uuid'] = $this->defining_element_uuid;
        $ret['has_events'] = $this->has_events;
        $ret['created_at'] = $this->created_at;

        if ($this->defining_element) {
            $ret['defining_element'] = $this->defining_element;
        }

        if ($this->parent_set) {
            $ret['parent_set'] = $this->parent_set;
        }

        if (count($this->children_sets )) {
            $ret['children_sets'] = $this->children_sets;
        }

        if (count($this->member_elements )) {
            $ret['member_elements'] = $this->member_elements;
        }
        return $ret;
    }

}
