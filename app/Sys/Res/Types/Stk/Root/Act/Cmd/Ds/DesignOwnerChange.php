<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\OpenApi\Params\Actioning\Design\DesignOwnershipParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;


#[HexbatchTitle( title: "Change the ownership of a design")]
#[HexbatchBlurb( blurb: "Unpublished designs have their ownership changed here, this can be refused by the otherwise new owner")]
#[HexbatchDescription( description: /** @lang markdown */
    '

   # Design ownership changed

    A design can be given to some other namespace

    The future type owner will get an event, and the admin group to the type has start this


   * [ElementTypeTurningOff](../../../Evt/Server/TypeOwnerChanging.php)

   if the new owner agress, or does not have an event handler set, then the ownership is changed

   and the older and new type owners and type owners gets the following

   * [ElementTypeTurnedOff](../../../Evt/Server/TypeOwnerChanged.php)
')]
#[ApiParamMarker( param_class: DesignOwnershipParams::class)]
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
        Evt\Server\TypeOwnerChanging::class,
        Evt\Server\TypeOwnerChanged::class
    ];


    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();
        $this->checkIfAdmin($this->getGivenType()->owner_namespace);


    }

    protected function postActionInner(array $data = []): void {
        if ($this->send_event) {
            $this->post_events_to_send = Evt\Server\TypeOwnerChanged::makeEventActions(
                source: $this, action_data: $this->action_data,
                type_context: $this->getGivenType()
            );
        }
    }



    public function getChildrenTree(): ?Tree
    {
        if (!$this->send_event) {return null;}
        $nodes = [];
        $events = [];
        if ($this->getGivenType() && $this->getGivenNamespace()) {
            if ($this->getGivenType()->ref_uuid !== $this->getGivenNamespace()->ref_uuid) {
                $events = Evt\Server\TypeOwnerChanging::makeEventActions(source: $this, action_data: $this->action_data,
                    type_context: $this->getGivenType(),namespace_context: $this->getGivenNamespace());
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

    /**
     * @throws \Exception
     */
    public function setChildActionResult(IThingAction $child): void
    {


        if ($child instanceof Evt\Server\TypeOwnerChanging) {

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

