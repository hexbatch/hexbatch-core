<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Locations\Location;
use App\Enums\Bounds\TypeOfLocation;
use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\LocationBound;
use App\Models\UserNamespace;

use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
class DesignLocationCreate extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = 'f26dcdcb-09e4-41df-b435-3e7b106c6282';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LOCATION_CREATE;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];



    const array ACTIVE_DATA_KEYS = ['bound_name','given_location_uuid','location_type','geo_json','display','is_deleting'];

    #[ApiParamMarker( param_class: Location::class)]
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
        $this->setGivenLocationBound($this->given_location_uuid);
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
     * @throws \Throwable
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
            $ret['bound'] = Location::validateAndCreate($what['bound']);
        }
        return $ret;
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $params = Location::validateAndCreate($command_args['location_params']);
        $namespace = $command_args['namespace'];
        $bound = $command_args['given_bound']??null;
        $new_location = static::createLocationBound(params: $params,namespace: $namespace,given_bound: $bound);
        Log::debug("Called design location create node",['args'=>$command_args,'location'=>$new_location]);
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $new_location->toArray());
    }

    /**
     * @throws \Throwable
     */
    protected static function createLocationBound(Location $params, UserNamespace $namespace, ?LocationBound $given_bound = null) : LocationBound
    {
        if ($given_bound) {
            static::checkIfGivenIsAdmin(given: $namespace,target: $given_bound->location_namespace);
        }
        DB::transaction(function () use($params,$namespace,&$given_bound){
            $collect = new Collection(
                [
                    'bound_name' => $params->bound_name,
                    'location_type' => $params->location_type,
                    'geo_json' => $params->geo_json,
                    'display' => $params->location_display
                ]
            );
            if ($given_bound) {
                $given_bound = LocationBound::collectLocationBound(collect: $collect,bound: $given_bound);
            } else {
                $given_bound = LocationBound::collectLocationBound(collect: $collect,namespace: $namespace);
            }
        });
        return $given_bound;
    }
}

