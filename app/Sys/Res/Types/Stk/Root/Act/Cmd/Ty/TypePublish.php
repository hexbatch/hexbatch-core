<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfLifecycle;
use App\Models\ActionDatum;
use App\Models\ElementType;
use App\Sys\Res\Atr\Stk\Act\Metrics\TypePublishMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt\Server\TypePublished;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Facades\DB;

/**
 * Publishes the type, any referenced parent types, parent attributes, live rules, live requirements
 * are given the event of @see TypePublished and all must agree
 *
 */
class TypePublish extends Act\Cmd\Ty
{
    const UUID = 'af28da1b-b148-4cbf-a53f-ccaf641373ea';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_PUBLISH;


    const ATTRIBUTE_CLASSES = [
        TypePublishMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\TypePublished::class
    ];


    public function getPublishingType(): ElementType
    {
        return $this->action_data->data_type;
    }


    const array ACTIVE_DATA_KEYS = ['given_type_uuid'];


    public function __construct(
        protected string              $given_type_uuid ,
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


    /**
     * @throws \Exception
     */
    public function runAction(array $data = []): void
    {
        parent::runAction($data);

        if ($this->getPublishingType()->lifecycle === TypeOfLifecycle::PUBLISHED) {
            throw new \RuntimeException("Type already published");
        }

        if (!$this->is_system) {
            if ($this->getPublishingType()->canBePublished()) {
                throw new \RuntimeException("Cannot be published");
            }
        }
        try {
            DB::beginTransaction();
            $this->getPublishingType()->lifecycle = TypeOfLifecycle::PUBLISHED;
            $this->getPublishingType()->save();

            if ($this->send_event) {
                $this->post_events_to_send =  Evt\Server\TypePublished::makeEventActions(source: $this,
                    data: $this->action_data,
                    type_context: $this->getPublishingType());
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
        return ['type'=>$this->getPublishingType()];
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->action_data->data_type_id = ElementType::getElementType(uuid: $this->given_type_uuid)->id;
        $this->action_data->save();
        return $this->action_data;
    }

}
