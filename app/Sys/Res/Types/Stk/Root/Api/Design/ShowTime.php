<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;



use App\Data\ApiParams\Data\Schedules\Schedule;

use App\Models\TimeBound;

use App\Sys\Res\Types\Stk\Root\Api;

class ShowTime extends Api\DesignApi
{
    const UUID = '80daa284-e81c-432d-a2ae-9f84bed9cf2f';
    const TYPE_NAME = 'api_design_show_time';


    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];


    public static function showSchedule(TimeBound $given_bound) : Schedule {
        $given_bound->loadMissing('time_spans','schedule_namespace','scheduled_types');
        return Schedule::validateAndCreate($given_bound);
    }

}

