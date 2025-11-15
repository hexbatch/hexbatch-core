<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;



use App\Data\ApiParams\Data\Schedules\Schedule;
use App\Models\ActionDatum;
use App\Models\TimeBound;

use App\Sys\Res\Types\Stk\Root\Api;

class ShowTime extends Api\DesignApi
{
    const UUID = '80daa284-e81c-432d-a2ae-9f84bed9cf2f';
    const TYPE_NAME = 'api_design_show_time';


    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];


    public function __construct(
        protected TimeBound $bound,
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

    public function getDataSnapshot(): Schedule
    {
        $what =  $this->getMyData();
        return Schedule::validateAndCreate($what['bound']->toArray());
    }

    public static function showSchedule(TimeBound $bound) {
        $bound->loadMissing('time_spans');
        return Schedule::validateAndCreate($bound);
    }

}

