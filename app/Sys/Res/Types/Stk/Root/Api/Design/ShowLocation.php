<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;



use App\Data\ApiParams\Casts\FromBoxToArray;
use App\Data\ApiParams\Data\Locations\Location;
use App\Models\ActionDatum;
use App\Models\LocationBound;

use App\Sys\Res\Types\Stk\Root\Api;

class ShowLocation extends Api\DesignApi
{
    const UUID = 'fd657291-dee2-4b4e-a971-32c0b08c455a';
    const TYPE_NAME = 'api_design_show_location';


    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];


    public function __construct(
        protected LocationBound $bound,
        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init,
            is_async: $this->is_async,tags: $this->tags);
    }


    protected function getMyData() :array {
        return ['bound'=>$this->bound];
    }

    public function getDataSnapshot(): Location
    {
        $what =  $this->getMyData();
        return Location::validateAndCreate($what['bound']->toArray());
    }

    public static function showSchedule(LocationBound $bound) : Location {
        $bound->loadMissing('location_namespace');
        $bound->shape_bounding_box = FromBoxToArray::fromBoxtoArray($bound->shape_bounding_box);
        $bound->map_bounding_box = FromBoxToArray::fromBoxtoArray($bound->map_bounding_box);
        return Location::validateAndCreate($bound);
    }

}

