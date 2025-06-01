<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Models\ActionDatum;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Deletes a design by system")]
#[HexbatchBlurb( blurb: "No permission checks, no events raised")]
#[HexbatchDescription( description:'')]
class DesignPurge extends Act\Cmd\Ds
{
    const UUID = '39693e91-d477-4a68-a8ba-7b8a41e94718';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_PURGE;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class,
        Act\SystemPrivilege::class
    ];

    const array ACTIVE_DATA_KEYS = ['given_type_uuid'];
    public function __construct(
        protected ?string        $given_type_uuid = null,

        protected ?bool          $is_async = null,
        protected ?ActionDatum   $action_data = null,
        protected ?ActionDatum   $parent_action_data = null,
        protected ?UserNamespace $owner_namespace = null,
        protected bool                     $is_system = false,
        protected bool                     $send_event = true,
        protected bool           $b_type_init = false,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        if ($this->given_type_uuid) {
            $this->action_data->data_type_id = ElementType::getElementType(uuid: $this->given_type_uuid)->id;
        }
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

        if (!$this->getDesignType()) {
            throw new \InvalidArgumentException("Need type before can delete it");
        }


        try {
            DB::beginTransaction();
            $this->getDesignType()->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }
    //todo make system types public domain except some

    protected function getMyData() :array {
        return ['attribute'=>$this->getAttribute()];
    }


}

