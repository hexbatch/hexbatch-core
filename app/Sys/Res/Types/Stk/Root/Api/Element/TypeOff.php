<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\Params\Actioning\Element\ElementSelectParams;
use App\OpenApi\Results\Elements\ElementActionResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Collection;


#[ApiParamMarker( param_class: ElementSelectParams::class)]
class TypeOff extends Api\ElementApi
{
    const UUID = '2a8f43d7-62b1-4776-9868-42a31de9035d';
    const TYPE_NAME = 'api_element_type_off';

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\TypeOff::class,
    ];


    public function __construct(
        protected ?ElementSelectParams $params = null,

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
            $this->params = new ElementSelectParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    protected function getMyData() :array {
        return [
            'element'=>$this->getGivenElement(),
            'set'=>$this->getGivenSet(),
            'type'=>$this->getGivenType(),
            'phase'=>$this->getGivenPhase(),
        ];
    }

    public function getDataSnapshot(): array
    {
        $ret =  $this->getMyData();
        $what = [];
        $what['action'] = new ElementActionResponse(given_element: $ret['element'],given_set: $ret['set'],given_type: $ret['type'],given_phase: $ret['phase']);
        return $what;
    }


    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ele\TypeOff(
            given_set_uuid: $this->params->getSetRef(),
            given_element_uuid: $this->params->getFirstElementRef(),
            given_type_uuid: $this->params->getTypeRef(),
            given_phase_uuid: $this->params->getPhaseRef(),
        );
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1,
            'title' => 'Type off '. $creator->getGivenType()->getName(),
            'action'=>$creator];


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

        if ($child instanceof Act\Cmd\Ele\TypeOff) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenPhase($child->getGivenPhase());
                    $this->setGivenType($child->getGivenPhase());
                    $this->setGivenElement($child->getGivenPhase());
                    $this->setGivenSet($child->getGivenSet());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

