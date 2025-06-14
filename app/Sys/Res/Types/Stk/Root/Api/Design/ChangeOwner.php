<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\Params\Design\DesignOwnershipParams;
use App\OpenApi\Types\TypeResponse;
use App\OpenApi\UserNamespaces\UserNamespaceResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IHookCode;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Collection;


#[ApiParamMarker( param_class: DesignOwnershipParams::class)]
class ChangeOwner extends Api\DesignApi implements IHookCode
{
    const UUID = '1a222e21-c548-4555-95ad-74aee1387f17';
    const TYPE_NAME = 'api_design_change_owner';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignOwnerChange::class,
    ];

    public function __construct(
        protected ?DesignOwnershipParams $params = null,

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
            $this->params = new DesignOwnershipParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    protected function getMyData() :array {
        return ['type'=>$this->getGivenType(),'namespace'=>$this->getGivenNamespace()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['type'])) {
            $ret['type'] = new TypeResponse(given_type:  $what['type']);
        }
        if (isset($what['namespace'])) {
            $ret['namespace'] = new UserNamespaceResponse(namespace:  $what['type']);
        }

        return $ret;
    }






    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignOwnerChange(
            given_type_uuid: $this->params->getTypeUuid(), given_namespace_uuid: $this->params->getNamespaceUuid(),
            parent_action_data: $this->action_data,
            tags: ['changing owner from api']);

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

        if ($child instanceof Act\Cmd\Ds\DesignOwnerChange) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenType($child->getGivenType());
                    $this->setGivenNamespace($child->getGivenNamespace());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

