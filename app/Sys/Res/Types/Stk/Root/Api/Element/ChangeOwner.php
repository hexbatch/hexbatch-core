<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\Params\Actioning\Element\ChangeElementOwnerParams;
use App\OpenApi\Results\Elements\ElementCollectionResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Illuminate\Support\Collection;


#[ApiParamMarker( param_class: ChangeElementOwnerParams::class)]
class ChangeOwner extends Api\ElementApi
{
    const UUID = '513a16a3-cbb5-4f6e-a6e4-4e7b90b0a1c6';
    const TYPE_NAME = 'api_element_change_owner';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\ElementOwnerChange::class,
    ];

    public function __construct(
        protected ?ChangeElementOwnerParams $params = null,

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
            $this->params = new ChangeElementOwnerParams();
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
        $creator = new Act\Cmd\Ele\ElementOwnerChange(
            given_element_uuids: $this->params->getElementRefs(),
            given_new_namespace_uuid: $this->params->getNamespaceRef()
        );
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1, 'title' => 'Change owner of '.$creator->getGivenElement()->getName(),'action'=>$creator];


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

        if ($child instanceof Act\Cmd\Ele\ElementOwnerChange) {
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

