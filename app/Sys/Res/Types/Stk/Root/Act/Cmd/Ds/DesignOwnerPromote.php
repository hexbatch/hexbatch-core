<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\OpenApi\Types\TypeResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Change a design owner")]
#[HexbatchBlurb( blurb: "System can change any design owner")]
#[HexbatchDescription( description:'')]
class DesignOwnerPromote extends Act\Cmd\Ds
{
    const UUID = '3feda9e3-e732-41b0-88c6-3d3f45e83bf4';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_OWNER_PROMOTE;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,

    ];

    const array ACTIVE_DATA_KEYS = ['given_type_uuid','given_namespace_uuid'];
    public function __construct(
        protected ?string        $given_type_uuid = null,
        protected ?string        $given_namespace_uuid = null,

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

        $this->setGivenNamespace( $this->given_namespace_uuid)->setGivenType($this->given_type_uuid);

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
            throw new \InvalidArgumentException("Need type before can change type owner");
        }

        if (!$this->getGivenNamespace()) {
            throw new \InvalidArgumentException("Need namespace before can change type owner");
        }

        try {
            DB::beginTransaction();
            $this->getDesignType()->owner_namespace_id = $this->getGivenNamespace()->id ;
            $this->getDesignType()->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




    protected function getMyData() :array {
        return ['type'=>$this->getGivenType(),'namespace'=>$this->getGivenNamespace()];
    }

    public function getDataSnapshot(): array
    {
        $ret = [];
        $what =  $this->getMyData();
        if (isset($what['type'])) {
            $ret['type'] = new TypeResponse(given_type: $what['type']);
        }
        return $ret;
    }

}

