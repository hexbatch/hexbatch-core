<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Models\ActionDatum;
use App\Models\Attribute;

use App\Models\UserNamespace;
use App\OpenApi\Attributes\AttributeResponse;
use App\Sys\Res\Types\Stk\Root\Act;

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
        protected bool                     $b_type_init = false,
        protected bool                     $is_system = false,
        protected bool                     $send_event = true,
        protected array                    $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->setGivenAttribute($this->given_attribute_uuid);

        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }


    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();

        if (!$this->getDesignAttribute()) {
            throw new \InvalidArgumentException("Need attribute before can delete it");
        }
        $this->checkIfAdmin($this->getDesignAttribute()->type_owner?->owner_namespace);

        try {
            DB::beginTransaction();
            $this->getDesignAttribute()->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }




    protected function getMyData() :array {
        return ['attribute'=>$this->getAttribute()];
    }

    public function getDataSnapshot(): array
    {
        $ret = [];
        $what =  $this->getMyData();
        if (isset($what['attribute'])) {
            $ret['attribute'] = new AttributeResponse(given_attribute: $what['attribute']);
        }
        return $ret;
    }


}

