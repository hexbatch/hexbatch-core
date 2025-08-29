<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\Params\Set\SetCreateParams;
use App\OpenApi\Set\SetResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Collection;


#[ApiParamMarker( param_class: SetCreateParams::class)]
class CreateSet extends Api\SetApi
{
    const UUID = '7255ea40-d9f7-40d3-87c8-442269c77c96';
    const TYPE_NAME = 'api_element_create_set';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\SetCreate::class,
    ];

    public function __construct(
        protected ?SetCreateParams $params = null,

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
            $this->params = new SetCreateParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    protected function getMyData() :array {
        return ['set'=>$this->getGivenSet()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['set'])) {
            $ret['set'] = new SetResponse(given_set:  $what['set']);
        }

        return $ret;
    }






    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ele\SetCreate(
            given_element_uuid: $this->params->getElementRef(),
            given_parent_set_uuid: $this->params->getParentSetRef(),
            set_has_events: $this->params->hasEvents(),
            );
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1, 'title' => $creator->getGivenElement()->getName(),'action'=>$creator];


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

        if ($child instanceof Act\Cmd\Ele\SetCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenSet($child->getGivenSet());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

