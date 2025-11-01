<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Schedules\ScheduleParams;
use App\Models\ActionDatum;
use App\OpenApi\ApiResults\Bounds\ApiScheduleResponse;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Hexbatch\Things\Interfaces\IThingBaseResponse;

#[ApiParamMarker( param_class: ScheduleParams::class)]
class CreateTime extends Api\DesignApi
{
    const UUID = 'b3b52738-f425-4083-9648-e777837696b7';
    const TYPE_NAME = 'api_design_create_time';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignTimeCreate::class,
    ];


    public function __construct(
        protected ?ScheduleParams $params = null,

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
        return ['bound'=>$this->getGivenTimeBound()];
    }

    public function getDataSnapshot(): array|IThingBaseResponse
    {
        $what =  $this->getMyData();
        return new ApiScheduleResponse(given_time:  $what['bound'],thing: $this->getMyThing());
    }






    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignTimeCreate(
            bound_name: $this->params->bound_name,
            bound_start: $this->params->bound_start,
            bound_stop: $this->params->bound_stop,
            bound_cron: $this->params->bound_cron,
            bound_cron_timezone: $this->params->bound_cron_timezone,
            bound_period_length: $this->params->bound_period_length,
            parent_action_data: $this->action_data,tags: ['create time bound from api']);
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1, 'title' => $creator->getType()->getName(),'action'=>$creator];


        //last in tree is the
        if (count($nodes)) {
            return new Tree(
                $nodes,
                ['rootId' => -1]
            );
        }
        return null;

    }


    /**
     * @throws \Exception
     */
    public function setChildActionResult(IThingAction $child): void {

        if ($child instanceof Act\Cmd\Ds\DesignTimeCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenTimeBound($child->getGivenTimeBound());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

