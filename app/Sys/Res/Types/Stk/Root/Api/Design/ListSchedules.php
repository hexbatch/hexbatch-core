<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Schedules\Params\ScheduleSearchParams;
use App\Data\ApiParams\Data\Schedules\Schedule;
use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\TimeBound;
use App\Models\UserNamespace;

use App\Sys\Res\Types\Stk\Root\Api;

use Spatie\LaravelData\CursorPaginatedDataCollection;
use Symfony\Component\HttpFoundation\Response as CodeOf;

#[ApiParamMarker( param_class: ScheduleSearchParams::class)]
class ListSchedules extends Api\DesignApi
{
    const UUID = '5dc7b23e-c330-4cf6-8701-4e5db3c49946';
    const TYPE_NAME = 'api_design_list_schedules';


    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];

    public function __construct(
        protected ?ScheduleSearchParams $params = null,

        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init,
            is_async: $this->is_async,tags: $this->tags);
    }


    const PRIMARY_SNAPSHOT_KEY = 'schedules';
    const int HTTP_CODE_GOOD = CodeOf::HTTP_OK;

    protected function getMyData() :array {
        $namespace_id = null;
        if ($this->params->namespace_ref) {
            $namespace_id = UserNamespace::resolveNamespace(value: $this->params->namespace_ref)->id;
        }
        $build = TimeBound::buildTimeBound(
            namespace_id: $namespace_id,
            after_when: $this->params->after,
            before_when: $this->params->before,
            during_when: $this->params->during
        );

        return [static::PRIMARY_SNAPSHOT_KEY=>$build->cursorPaginate(cursor: $this->params->cursor)];
    }


    public function getDataSnapshot(): CursorPaginatedDataCollection
    {
        $what =  $this->getMyData();
        $schedules = $what[static::PRIMARY_SNAPSHOT_KEY];
        $resp = Schedule::collect($schedules, CursorPaginatedDataCollection::class);
        return $resp;
    }

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
        $cursor = $build->cursorPaginate(perPage: 2, cursor: $params->cursor);
        return Schedule::collect($cursor, CursorPaginatedDataCollection::class);
    }


}

