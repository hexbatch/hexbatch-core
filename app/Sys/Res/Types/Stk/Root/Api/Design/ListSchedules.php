<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\Models\TimeBound;
use App\OpenApi\Params\Listing\Design\ListScheduleParams;
use App\OpenApi\Results\Bounds\ScheduleCollectionResponse;
use App\Sys\Res\Types\Stk\Root\Api;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as CodeOf;

#[ApiParamMarker( param_class: ListScheduleParams::class)]
class ListSchedules extends Api\DesignApi
{
    const UUID = '5dc7b23e-c330-4cf6-8701-4e5db3c49946';
    const TYPE_NAME = 'api_design_list_schedules';


    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];

    public function __construct(
        protected ?ListScheduleParams $params = null,

        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init,
            is_async: $this->is_async,tags: $this->tags);
    }

    protected function restoreParams(array $param_array) {
        parent::restoreParams($param_array);
        if(!$this->params) {
            $this->params = new ListScheduleParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    const PRIMARY_SNAPSHOT_KEY = 'schedules';
    const int HTTP_CODE_GOOD = CodeOf::HTTP_OK;

    protected function getMyData() :array {
        $build = TimeBound::buildTimeBound(
            namespace_id: $this->params->getGivenNamespace()?->id,
            after_when: $this->params->getAfter(),
            before_when: $this->params->getBefore(),
            during_when: $this->params->getDuring()
        );

        return [static::PRIMARY_SNAPSHOT_KEY=>$build->cursorPaginate(cursor: $this->params->getCursor())];
    }


    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what[static::PRIMARY_SNAPSHOT_KEY])) {
            $ret[static::PRIMARY_SNAPSHOT_KEY] = new ScheduleCollectionResponse(given_schedules:  $what['schedules']);
        }
        return $ret;
    }


}

