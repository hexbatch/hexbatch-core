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
class ReadAttribute extends Api\ElementApi
{
    const UUID = 'ae6b7b0e-8991-4443-9f00-3e9a637a52ce';
    const TYPE_NAME = 'api_element_read';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Read::class,
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
        return ['element'=>$this->getGivenElement(),'set'=>$this->getGivenSet(),
            'phase'=>$this->getGivenPhase(),'value'=>$this->getImportantValue(),'attribute'=>$this->getGivenAttribute()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        $ret['read_value'] = new ElementActionResponse(value: $what['value'], given_element: $what['element'],
            given_set: $what['set'], given_phase: $what['phase']);

        return $ret;
    }


    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ele\Read(
            given_set_uuid: $this->params->getSetRef(),
            given_element_uuid: $this->params->getFirstElementRef(),
            given_attribute_uuid: $this->params->getAttributeRef(),
            given_phase_uuid: $this->params->getPhaseRef()
        );
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1, 'title' => 'Destroying Elements','action'=>$creator];


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

        if ($child instanceof Act\Cmd\Ele\Read) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenPhase($child->getGivenPhase());
                    $this->setImportantValue($child->getImportantValue());
                    $this->setGivenElement($child->getGivenElement());
                    $this->setGivenSet($child->getGivenSet());
                    $this->setGivenAttribute($child->getGivenAttribute());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

