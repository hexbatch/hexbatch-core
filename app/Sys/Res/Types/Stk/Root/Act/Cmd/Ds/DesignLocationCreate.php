<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Bounds\TypeOfLocation;
use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\LocationBound;
use App\Models\UserNamespace;
use App\OpenApi\Bounds\LocationResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Create a location")]
#[HexbatchBlurb( blurb: "Create a 2d map bounds or a 3d shape")]
#[HexbatchDescription( description:'
# create a location bound
* bound_uuid if editing
* bound_name
* location_type
* geo_json
* display

')]
class DesignLocationCreate extends Act\Cmd\Ds
{
    const UUID = 'f26dcdcb-09e4-41df-b435-3e7b106c6282';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LOCATION_CREATE;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];



    const array ACTIVE_DATA_KEYS = ['bound_name','given_location_uuid','location_type','geo_json','display','is_deleting'];


    public function __construct(
        protected ?string           $bound_name =null,
        protected ?string           $given_location_uuid = null,
        protected ?TypeOfLocation   $location_type = null,
        protected null|string|array $geo_json = null,
        protected null|string|array $display = null,
        protected ?string           $uuid = null,
        protected bool              $is_deleting = false,
        protected bool              $is_system = false,
        protected bool              $send_event = true,
        protected ?bool             $is_async = null,
        protected ?ActionDatum      $action_data = null,
        protected ?ActionDatum      $parent_action_data = null,
        protected ?UserNamespace    $owner_namespace = null,
        protected bool                $b_type_init = false,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
    }



    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data) {
            if ($this->action_data->collection_data?->offsetExists('location_type')) {
                $location_string = $this->action_data->collection_data->offsetGet('access');
                $this->location_type = TypeOfLocation::tryFromInput($location_string);
            }
        }
    }

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);

        $this->action_data->collection_data->offsetSet('location_type',$this->location_type?->value);
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

    public function getInitialConstantData(): array {
        $ret = parent::getInitialConstantData();
        $ret['location_type'] = $this->location_type?->value;
        return $ret;
    }


    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();
        if ($this->getGivenLocationBound()) {

            if ($this->is_deleting) {
                $this->checkIfAdmin($this->getGivenLocationBound()->location_namespace);
                if ($this->getGivenLocationBound()->isInUse()) {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                } else {
                    try {
                        DB::beginTransaction();
                        $this->getGivenLocationBound()->delete();
                        $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        throw $e;
                    }
                }
                return;
            }
        }

        $this->checkIfAdmin($this->getGivenLocationBound()->location_namespace);

        try {
            DB::beginTransaction();

            $collect = new Collection(
                [
                    'bound_name' => $this->bound_name,
                    'location_type' => $this->location_type,
                    'geo_json' => $this->geo_json,
                    'display' => $this->display,
                ]
            );
            if ($bound = $this->getGivenLocationBound()) {
                LocationBound::collectLocationBound(collect: $collect,bound: $bound);
            } else {
                $bound = LocationBound::collectLocationBound(collect: $collect,namespace: $this->getOwningNamespace());
                $this->given_location_uuid = $bound->ref_uuid;
                $this->initData();
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




    protected function getMyData() :array {
        return ['bound'=>$this->getGivenLocationBound()];
    }

    public function getDataSnapshot(): array
    {
        $ret = [];
        $what =  $this->getMyData();
        if (isset($what['bound'])) {
            $ret['bound'] = new LocationResponse(given_location: $what['bound']);
        }
        return $ret;
    }

}

