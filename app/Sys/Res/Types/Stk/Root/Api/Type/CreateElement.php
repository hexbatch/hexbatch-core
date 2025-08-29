<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\Elements\ElementCollectionResponse;
use App\OpenApi\Params\Type\CreateElementParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Collection;

/**
 *   if no set provided, it will put new element in the caller's home set.
 */
#[ApiParamMarker( param_class: CreateElementParams::class)]
class CreateElement extends Api\ElementApi
{
    const UUID = 'bad981d1-f817-4f89-879c-3d2d9c6443b6';
    const TYPE_NAME = 'api_types_create_element';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ty\ElementCreate::class,
    ];

    public function __construct(
        protected ?CreateElementParams $params = null,

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
            $this->params = new CreateElementParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    protected function getMyData() :array {
        return ['elements'=>$this->getGivenElements()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['elements'])) {
            $ret['elements'] = new ElementCollectionResponse(given_elements:  $what['elements']);
        }

        return $ret;
    }






    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ty\ElementCreate(
            given_type_uuid: $this->params->getTypeRef(),
            given_namespace_uuid: $this->params->getNamespaceRef(),
            given_phase_uuid: $this->params->getPhaseRef(),
            number_to_create: $this->params->getNumberToCreate(),
        );
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1, 'title' => 'Elements of '. $creator->getGivenType()->getName(),'action'=>$creator];


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

        if ($child instanceof Act\Cmd\Ty\ElementCreate) {
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

