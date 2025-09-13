<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\Params\Actioning\Design\DesignParentParams;
use App\OpenApi\Results\Types\TypeResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as CodeOf;

#[ApiParamMarker( param_class: DesignParentParams::class)]
class AddParent extends Api\DesignApi
{
    const UUID = 'abaa730a-bfe2-4437-bafe-493776ac1ca7';
    const TYPE_NAME = 'api_design_add_parent';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignParentAdd::class,
    ];

    const int HTTP_CODE_GOOD = CodeOf::HTTP_ACCEPTED;

    public function __construct(
        protected ?DesignParentParams $params = null,

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
            $this->params = new DesignParentParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    protected function getMyData() :array {
        return ['type'=>$this->getGivenType()];
    }

    public function getDataSnapshot(): array|IThingBaseResponse
    {
        $what =  $this->getMyData();
        return new TypeResponse(given_type:  $what['type'],thing: $this->getMyThing());
    }



    public function getChildrenTree(): ?Tree
    {
        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignParentAdd(
            given_type_uuid: $this->params->getTypeUuid(), given_parent_uuids: $this->params->getParentUuids(),
            parent_action_data: $this->action_data,tags: ['add parents from api']);
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

        if ($child instanceof Act\Cmd\Ds\DesignParentAdd) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenType($child->getGivenType());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

