<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\Params\Actioning\Element\ElementSelectParams;
use App\OpenApi\Results\Elements\ElementCollectionResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Illuminate\Support\Collection;


#[ApiParamMarker( param_class: ElementSelectParams::class)]
class Destroy extends Api\ElementApi
{
    const UUID = 'bd9d7481-5f47-4bd6-8ec0-90f4df0c91be';
    const TYPE_NAME = 'api_element_destroy';



    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\ElementDestroy::class,
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
        return ['elements'=>$this->getGivenElements()];
    }

    public function getDataSnapshot(): array|IThingBaseResponse
    {
        $what =  $this->getMyData();
        return new ElementCollectionResponse(given_elements:  $what['elements'],thing: $this->getMyThing());
    }


    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ele\ElementDestroy(
            given_element_uuids: $this->params->getElementRefs()
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

        if ($child instanceof Act\Cmd\Ele\ElementDestroy) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenElements($child->getGivenElements());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

