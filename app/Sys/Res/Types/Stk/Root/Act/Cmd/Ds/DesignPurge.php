<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\UserNamespace;
use App\OpenApi\Params\Actioning\Type\TypeParams;
use App\OpenApi\Results\Types\TypeResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Deletes a design by system")]
#[HexbatchBlurb( blurb: "No permission checks, no events raised")]
#[HexbatchDescription( description:'')]
#[ApiParamMarker( param_class: TypeParams::class)]
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

    const CHECK_PERMISSION = false;

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
        $this->setGivenType($this->given_type_uuid);
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

        if (!$this->getGivenType()) {
            throw new \InvalidArgumentException("Need type before can delete it");
        }

        if (static::CHECK_PERMISSION) {
            $this->checkIfAdmin($this->getGivenType()->owner_namespace);
        }

        try {
            DB::beginTransaction();
            $this->getGivenType()->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }


    protected function getMyData() :array {
        return ['type'=>$this->getGivenType()];
    }


    public function getDataSnapshot(): array
    {
        $ret = [];
        $what =  $this->getMyData();
        if (isset($what['type'])) {
            $ret['type'] = new TypeResponse(given_type: $what['type'],parent_levels: 1);
        }
        return $ret;
    }


}

