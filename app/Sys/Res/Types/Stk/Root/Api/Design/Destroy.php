<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\Params\Design\DesignDestroyParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IHookCode;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Collection;


#[ApiParamMarker( param_class: DesignDestroyParams::class)]
class Destroy extends Api\DesignApi implements IHookCode
{
    const UUID = '74ff2b6e-4b93-4db1-b8fe-c3eb672cc16b';
    const TYPE_NAME = 'api_design_destroy';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignDestroy::class,
    ];


    public function __construct(
        protected ?DesignDestroyParams $params = null,

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
            $this->params = new DesignDestroyParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    protected function getMyData() :array {
        return ['type_uuid'=>$this->params->getTypeUuid()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['type_uuid'])) {
            $ret['type_uuid'] = $what['type_uuid'];
        }

        return $ret;
    }






    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignDestroy(
            given_type_uuid: $this->params->getTypeUuid(),tags: ['deleting design']);

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

        if ($child instanceof Act\Cmd\Ds\DesignDestroy) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess()) {
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                }
            }
        }
    }

}

