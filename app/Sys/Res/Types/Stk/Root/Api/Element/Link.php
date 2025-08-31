<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\Params\Actioning\Element\LinkCreateParams;
use App\OpenApi\Results\Set\LinkResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Collection;


#[ApiParamMarker( param_class: LinkCreateParams::class)]
class Link extends Api\ElementApi
{
    const UUID = 'af1e457d-7bc2-4467-8434-ae099a29123e';
    const TYPE_NAME = 'api_element_link';

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\LinkAdd::class,
    ];


    public function __construct(
        protected ?LinkCreateParams $params = null,

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
            $this->params = new LinkCreateParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    protected function getMyData() :array {
        return ['link'=>$this->getGivenLink()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['link'])) {
            $ret['link'] = new LinkResponse(linker:  $what['link']);
        }

        return $ret;
    }






    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ele\LinkAdd(
            given_set_uuid: $this->params->getTargetSetRef(),
            given_element_uuid: $this->params->getElementRef()
        );
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1,
            'title' => sprintf('link set %s of element %s ',$creator->getGivenSet()->getName(),$creator->getGivenElement()->getName()),
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

        if ($child instanceof Act\Cmd\Ele\LinkAdd) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenLink($child->getGivenLink());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

