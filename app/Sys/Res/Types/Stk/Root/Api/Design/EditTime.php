<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\OpenApi\Params\Design\DesignTimeParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;

#[ApiParamMarker( param_class: DesignTimeParams::class)]
class EditTime extends CreateTime
{
    const UUID = '0a0c55b3-a608-42b8-b9cc-373601e74757';
    const TYPE_NAME = 'api_design_edit_time';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignTimeEdit::class,
    ];


    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignTimeEdit(
            bound_name: $this->params->getBoundName(),
            given_time_uuid: $this->params->getBoundUuid(),
            bound_start: $this->params->getBoundStart(),
            bound_stop: $this->params->getBoundStop(),
            bound_cron: $this->params->getBoundCron(),
            bound_cron_timezone: $this->params->getBoundCronTimezone(),
            bound_period_length: $this->params->getBoundPeriodLength(),
            parent_action_data: $this->action_data, tags: ['edit time bound from api']);
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

        if ($child instanceof Act\Cmd\Ds\DesignTimeEdit) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenTimeBound($child->getGivenTimeBound());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

