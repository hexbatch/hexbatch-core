<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;

use App\Models\ActionDatum;
use App\Models\TimeBound;
use App\OpenApi\ApiResults\Bounds\ApiScheduleResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Hexbatch\Things\Interfaces\IThingBaseResponse;



class DestroyTime extends Api\DesignApi
{
    const UUID = 'd55e0d09-0830-4723-acbc-acb3595b7d57';
    const TYPE_NAME = 'api_design_destroy_time';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignTimeDestroy::class,
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

    public function getDataSnapshot(): array|IThingBaseResponse
    {
        $what =  $this->getMyData();
        return new ApiScheduleResponse(given_time:  $what['bound'],thing: $this->getMyThing());
    }


    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignTimeDestroy(
            given_time_uuid: $this->bound->ref_uuid,
             tags: ['edit time bound from api']);
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

        if ($child instanceof Act\Cmd\Ds\DesignTimeDestroy) {
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

