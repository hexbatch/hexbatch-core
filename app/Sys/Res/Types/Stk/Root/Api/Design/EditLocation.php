<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\OpenApi\Params\Actioning\Design\DesignLocationParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;

#[ApiParamMarker( param_class: DesignLocationParams::class)]
class EditLocation extends CreateLocation
{
    const UUID = '092ffcc2-8feb-4922-9325-fcb3d197886d';
    const TYPE_NAME = 'api_design_edit_location';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignLocationEdit::class,
    ];


    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignLocationEdit(
            bound_name: $this->params->getBoundName(),
            given_location_uuid: $this->params->getBoundUuid(),
            location_type: $this->params->getLocationType(),
            geo_json: $this->params->getGeoJson(),
            display: $this->params->getDisplay(),
            parent_action_data: $this->action_data,tags: ['edit location bound from api']);
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

        if ($child instanceof Act\Cmd\Ds\DesignLocationCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenLocationBound($child->getGivenLocationBound());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

