<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\Attributes\AttributeResponse;
use App\OpenApi\Params\Design\DesignAttributeParams;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Collection;

#[ApiParamMarker( param_class: DesignAttributeParams::class)]
class CreateAttribute extends Api\DesignApi
{
    const UUID = '745c1851-68af-4420-b6f9-037aa63bebc7';
    const TYPE_NAME = 'api_design_create_attribute';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignAttributeCreate::class,
    ];


    public function __construct(
        protected ?DesignAttributeParams $params = null,

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
            $this->params = new DesignAttributeParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    protected function getMyData() :array {
        return ['attribute'=>$this->getGivenAttribute()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['attribute'])) {
            $ret['attribute'] = new AttributeResponse(given_attribute:  $what['attribute']);
        }

        return $ret;
    }






    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignAttributeCreate(
            given_design_uuid: $this->params->getDesignUuid(),
            attribute_name: $this->params->getAttributeName(),
            owner_type_uuid: $this->params->getTypeUuid(),
            parent_attribute_uuid: $this->params->getParentUuid(),
            design_attribute_uuid: $this->params->getDesignUuid(),
            location_uuid: $this->params->getLocationUuid(),
            is_final: $this->params->isFinal(),
            is_abstract: $this->params->isAbstract(),
            read_json_path: $this->params->getReadJsonPath(),
            validate_json_path: $this->params->getValidateJsonPath(),
            default_value: $this->params->getDefaultValue(),
            access: $this->params->getAccess(),
            value_policy: $this->params->getValuePolicy(),
            parent_action_data: $this->action_data,tags: ['create attribute from api']);
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

        if ($child instanceof Act\Cmd\Ds\DesignAttributeCreate) {
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

