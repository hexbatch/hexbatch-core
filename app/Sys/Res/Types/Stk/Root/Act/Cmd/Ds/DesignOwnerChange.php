<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;


#[HexbatchTitle( title: "Change the ownership of a design")]
#[HexbatchBlurb( blurb: "Unpublished designs have their ownership changed here, this can be refused by the otherwise new owner")]
#[HexbatchDescription( description:'')]
class DesignOwnerChange extends DesignOwnerPromote
{
    const UUID = '3baa3285-5dff-42b5-bd22-071ad39101db';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_OWNER_CHANGE;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\TypeOwnerChange::class
    ];

    public function runAction(array $data = []): void
    {
        parent::runAction($data);

        $this->checkIfAdmin($this->getDesignType()->owner_namespace);
    }


    public function getChildrenTree(): ?Tree
    {
        if (!$this->send_event) {return null;}
        $nodes = [];
        $events = [];
        if ($this->getGivenType() && $this->getGivenNamespace()) {
            if ($this->getGivenType()->ref_uuid !== $this->getGivenNamespace()->ref_uuid) {
                $events = Evt\Server\TypeOwnerChange::makeEventActions(source: $this, action_data: $this->action_data,
                    type_context: $this->getDesignType(),namespace_context: $this->getGivenNamespace());
            }

        }

        foreach ($events as $event) {
            $nodes[] = ['id' => $event->getActionData()->id, 'parent' => -1, 'title' => $event->getType()->getName(), 'action' => $event];
        }

        if (count($nodes)) {
            return new Tree(
                $nodes,
                ['rootId' => -1]
            );
        }

        return null;
    }

    public function setChildActionResult(IThingAction $child): void
    {


        if ($child instanceof Evt\Server\TypeOwnerChange) {

            if ($child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            } else {

                if ($this->given_type_uuid === $child->getAskedAboutType()?->ref_uuid) {
                    if ($child->isActionFail()) {
                        $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                    }
                } //otherwise continue to set owner in run

            }
        } //end if this is design pending
    }

}

