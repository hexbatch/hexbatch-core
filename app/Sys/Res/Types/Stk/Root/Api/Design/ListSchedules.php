<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Schedules\Params\ScheduleSearchParams;
use App\Data\ApiParams\Data\Schedules\Schedule;
use App\Helpers\Utilities;
use App\Models\TimeBound;
use App\Models\UserNamespace;

use App\Sys\Res\Types\Stk\Root\Api;

use Spatie\LaravelData\CursorPaginatedDataCollection;


#[ApiParamMarker( param_class: ScheduleSearchParams::class)]
class ListSchedules extends Api\DesignApi
{
    const UUID = '5dc7b23e-c330-4cf6-8701-4e5db3c49946';
    const TYPE_NAME = 'api_design_list_schedules';


    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];


    /**
     * @return CursorPaginatedDataCollection<Schedule>
     */
    public static function listSchedules(?ScheduleSearchParams $params) {

        if ($params?->namespace_ref) {
            $namespace_id = UserNamespace::resolveNamespace(value: $params->namespace_ref)->id;
        } else {
            $namespace_id = Utilities::getCurrentNamespace()?->id;
        }
        $build = TimeBound::buildTimeBound(
            namespace_id: $namespace_id,
            after_when: $params?->after,
            before_when: $params?->before,
            during_when: $params?->during,
            with_spans: true
        )->orderBy('created_at');
        $cursor = $build->cursorPaginate(perPage: config('hbc.pagination.default_page_size'), cursor: $params->cursor);
        return Schedule::collect($cursor, CursorPaginatedDataCollection::class);
    }


}

