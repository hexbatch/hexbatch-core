<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;


use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfApproval;
use App\Models\ActionDatum;
use App\Models\ElementType;
use App\Models\ElementTypeParent;

use App\Sys\Res\Atr\Stk\Act\Metrics\DesignParentAddMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Facades\DB;


class DesignParentAdd extends Act\Cmd\Ds
{
    const UUID = '362a3cdf-f013-4bc0-afce-315cba179544';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_PARENT_ADD;

    const ATTRIBUTE_CLASSES = [
        DesignParentAddMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\DesignPending::class
    ];

    public function getDesignType(): ElementType
    {
        return $this->action_data->data_type;
    }

    /**
     * @return ElementType[]
     */
    public function getParents(): array
    {
        return $this->action_data->getCollectionOfType(ElementType::class);
    }

    const array ACTIVE_DATA_KEYS = ['given_type_uuid'];

    const array ACTIVE_COLLECTION_KEYS = ['given_parent_uuids'=>ElementType::class];
    public function __construct(
        protected string              $given_type_uuid ,
        /**
         * @var string[] $given_parent_uuids
         */
        protected array               $given_parent_uuids,
        protected ?TypeOfApproval     $approval = null,
        protected bool                $is_system = false,
        protected bool                $send_event = false,
        protected ?ActionDatum        $action_data = null,
        protected ?int                $action_data_parent_id = null,
        protected ?int                $action_data_root_id = null,
        protected bool                $b_type_init = false
    )
    {

        parent::__construct(action_data: $this->action_data, b_type_init: $this->b_type_init,
            is_system: $this->is_system, send_event: $this->send_event,
            action_data_parent_id: $this->action_data_parent_id, action_data_root_id: $this->action_data_root_id);
    }


    public function getActionPriority(): int
    {
        return 0;
    }

  /*
   * type the design
   * array uuid fo parent
   *
   */
    /**
     * @throws \Exception
     */
    public function runAction(array $data = []): void
    {
        parent::runAction($data);

        $approval = $this->approval?:TypeOfApproval::PENDING_DESIGN_APPROVAL;
        if (!$this->is_system) {
            $approval = TypeOfApproval::PENDING_DESIGN_APPROVAL;
        }
        try {
            DB::beginTransaction();
            foreach ($this->getParents() as $parent) {
                ElementTypeParent::addParent(parent: $parent, child: $this->getType(), init_approval: $approval);
                if ($this->send_event) {
                    $this->post_events_to_send = array_merge($this->post_events_to_send,
                        Evt\Server\DesignPending::makeEventActions(source: $this,data: $this->action_data,type_context: $parent));
                }

            }
            $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

    }



    protected function getMyData() :array {
        return ['type'=>$this->getDesignType()];
    }

    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data) {
            $approval_string = $this->action_data->collection_data->offsetGet('approval');
            $this->approval = TypeOfApproval::tryFromInput($approval_string);
        }
    }


    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->action_data->data_type_id = ElementType::getElementType(uuid: $this->given_type_uuid)->id;
        $this->action_data->collection_data->offsetSet('approval',$this->approval?->value);
        $this->action_data->save();
        return $this->action_data;
    }

}

