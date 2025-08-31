<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Exceptions\HexbatchFailException;
use App\Exceptions\RefCodes;
use App\Models\ActionDatum;
use App\Models\ElementType;
use App\Models\ElementTypeParent;
use App\Models\UserNamespace;
use App\OpenApi\Params\Actioning\Design\DesignParentParams;
use App\OpenApi\Results\Types\TypeResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Remove one or more parents")]
#[HexbatchBlurb( blurb: "Parents can be removed from the design without any events raised")]
#[HexbatchDescription( description:'')]
class DesignParentRemove extends Act\Cmd\Ds
{
    const UUID = 'bf333396-fdcc-45ac-977c-2a9be8f9840c';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_PARENT_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];



    /**
     * @return ElementType[]
     */
    public function getParents(): array
    {
        return $this->action_data->getCollectionOfType(ElementType::class);
    }

    const array ACTIVE_DATA_KEYS = ['given_type_uuid','check_permission'];

    const array ACTIVE_COLLECTION_KEYS = ['given_parent_uuids'=>ElementType::class];

    #[ApiParamMarker( param_class: DesignParentParams::class)]
    public function __construct(
        protected ?string              $given_type_uuid = null,
        /**
         * @var string[] $given_parent_uuids
         */
        protected array               $given_parent_uuids = [],
        protected bool                $check_permission = true,
        protected bool                $is_system = false,
        protected bool                $send_event = true,
        protected ?ActionDatum        $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool                $b_type_init = false,
        protected ?bool                $is_async = null,
        protected array             $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
    }




    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();
        if (!$this->getGivenType()) {
            throw new \InvalidArgumentException("Need type before can add parents to it");
        }

        if ($this->check_permission) {
            $this->checkIfAdmin($this->getGivenType()->owner_namespace);
        }


        try {
            DB::beginTransaction();
            $maybe_parent_ids = [];
            foreach ($this->getParents() as $parent) {
                $maybe_parent_ids[] = $parent->id;
            }
            /** @var ElementTypeParent[]|\Illuminate\Database\Eloquent\Collection $parent_records */
            $parent_records = ElementTypeParent::buildTypeParents(child_type_id:$this->getGivenType()->id,parent_ids: $maybe_parent_ids )->get();

            //check to see if they are parents in first list
            $parent_ids = [];
            foreach ($parent_records as $parent) {
                $parent_ids[] = $parent->id;
            }
            if (count($parent_ids) !== count($maybe_parent_ids)) {
                throw new HexbatchFailException( __('msg.parent_type_is_invalid_cannot_remove',['ref'=>$this->getGivenType()->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::TYPE_PARENT_CANNOT_BE_REMOVED);
            }

            foreach ($parent_records as $parent) {
                $parent->delete();
            }

            DB::commit();
        }

        catch (\Exception $e) {
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



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->setGivenType($this->given_type_uuid);
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }





}

