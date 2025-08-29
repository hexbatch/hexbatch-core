<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\OpenApi\Params\Element\ElementSelectParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;


#[ApiParamMarker( param_class: ElementSelectParams::class)]
class TypeOn extends TypeOff
{
    const UUID = '1570126c-e9b8-4fca-a525-078a74ce5ab1';
    const TYPE_NAME = 'api_element_type_on';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\TypeOn::class,
    ];

    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ele\TypeOn(
            given_set_uuid: $this->params->getSetRef(),
            given_element_uuid: $this->params->getFirstElementRef(),
            given_type_uuid: $this->params->getTypeRef(),
            given_phase_uuid: $this->params->getPhaseRef(),
        );
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1,
            'title' => 'Type off '. $creator->getGivenType()->getName(),
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

        if ($child instanceof Act\Cmd\Ele\TypeOn) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenPhase($child->getGivenPhase());
                    $this->setGivenType($child->getGivenPhase());
                    $this->setGivenElement($child->getGivenPhase());
                    $this->setGivenSet($child->getGivenSet());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

