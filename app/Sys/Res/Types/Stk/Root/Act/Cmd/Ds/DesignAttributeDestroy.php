<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Models\ActionDatum;
use App\Models\Attribute;

use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

use Hexbatch\Things\Enums\TypeOfThingStatus;

use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Destroys an attribute")]
#[HexbatchBlurb( blurb: "Attributes can be destroyed while in design phase. If type has approving parents, this is set back to pending and they are notified")]
#[HexbatchDescription( description:'')]
class DesignAttributeDestroy extends Act\Cmd\Ds
{
    const UUID = '079cfc62-0fa2-47f1-84c0-df0fa90441c5';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_DESTROY;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const array ACTIVE_DATA_KEYS = ['given_attribute_uuid'];
    public function __construct(
        protected ?string                  $given_attribute_uuid = null,

        protected ?bool                    $is_async = null,
        protected ?ActionDatum             $action_data = null,
        protected ?ActionDatum             $parent_action_data = null,
        protected ?UserNamespace           $owner_namespace = null,
        protected bool                     $b_type_init = false,protected int $priority = 0,
        protected array                    $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,priority: $this->priority,tags: $this->tags);
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        if ($this->given_attribute_uuid) {
            $this->action_data->data_attribute_id = Attribute::getThisAttribute(uuid: $this->given_attribute_uuid)->id;
        }
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }


    /**
     * @throws \Exception
     */
    public function runAction(array $data = []): void
    {
        parent::runAction($data);
        if ($this->isActionComplete()) {
            return;
        }

        if (!$this->getDesignAttribute()) {
            throw new \InvalidArgumentException("Need attribute before can delete it");
        }

        $this->checkIfAdmin($this->getDesignAttribute()->type_owner?->owner_namespace);


        try {
            DB::beginTransaction();
            $this->getDesignAttribute()->delete();
            $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            $this->action_data->refresh();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

    }



    protected function getMyData() :array {
        return ['attribute'=>$this->getAttribute()];
    }




}

