<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\OpenApi\Params\Design\DesignAttributeParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;

#[ApiParamMarker( param_class: DesignAttributeParams::class)]
class EditAttribute extends CreateAttribute
{
    const UUID = '40a60d68-5fb3-472d-9c90-bc033501ab1b';
    const TYPE_NAME = 'api_design_edit_attribute';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignAttributeEdit::class,
    ];


    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignAttributeEdit(
            given_design_uuid: $this->params->getDesignUuid(),
            uuid: $this->params->getUuid(),
            unset_parent: $this->params->isUnsetParent(),
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
            parent_action_data: $this->action_data,tags: ['edit attribute from api']);
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

        if ($child instanceof Act\Cmd\Ds\DesignAttributeEdit) {
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

