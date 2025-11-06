<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Schedules\Schedule;
use App\Enums\Sys\TypeOfAction;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\ActionDatum;
use App\Models\TimeBound;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Create a schedule")]
#[HexbatchBlurb( blurb: "Create a schedule using time rules")]
#[HexbatchDescription( description:'
# create a time bound
* bound_uuid if editing
* bound_name
* bound_start
* bound_stop
* bound_cron
* bound_cron_timezone
* bound_period_length
')]
class DesignTimeCreate extends Act\Cmd\Ds
{
    const UUID = '777c5080-dc81-40f8-8017-1a3a8a831a07';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_TIME_CREATE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];


    const array ACTIVE_DATA_KEYS = ['bound_name','given_time_uuid','bound_start','bound_stop','bound_cron_timezone','bound_period_length','is_deleting'];

    #[ApiParamMarker( param_class: Schedule::class)]
    public function __construct(
        protected ?string           $bound_name =null,
        protected ?string           $given_time_uuid = null,
        protected ?string           $bound_start = null,
        protected ?string           $bound_stop = null,
        protected ?string           $bound_cron = null,
        protected ?string           $bound_cron_timezone = null,
        protected ?string           $bound_period_length = null,
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


    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();

        if ($this->getGivenTimeBound()) {
            $this->checkIfAdmin($this->getGivenTimeBound()->schedule_namespace);
            if ($this->is_deleting) {

                if ($this->getGivenTimeBound()->isInUse()) {
                    throw new HexbatchNotPossibleException (
                        __('msg.bound_in_use',['ref'=>$this->getGivenTimeBound()->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::BOUND_IN_USE);
                } else {
                    try {
                        DB::beginTransaction();
                        $this->getGivenTimeBound()->delete();
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        throw $e;
                    }
                }
                return;
            }
        }
        try {
            DB::beginTransaction();

            $collect = new Collection(
                [
                    'bound_name' => $this->bound_name,
                    'bound_start' => $this->bound_start,
                    'bound_stop' => $this->bound_stop,
                    'bound_cron' => $this->bound_cron,
                    'bound_cron_timezone' => $this->bound_cron_timezone,
                    'bound_period_length' => $this->bound_period_length,
                ]
            );
            if ($bound = $this->getGivenTimeBound()) {
                TimeBound::collectTimeBound(collect: $collect,bound: $bound);
            } else {
                $bound = TimeBound::collectTimeBound(collect: $collect,namespace: $this->getOwningNamespace());
                $this->given_time_uuid = $bound->ref_uuid;
                $this->initData();
            }

            $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

    }

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->setGivenTimeBound($this->given_time_uuid);
        $this->action_data->refresh();
        return $this->action_data;
    }


    protected function getMyData() :array {
        return ['bound'=>$this->getGivenTimeBound()];
    }

    public function getDataSnapshot(): array
    {
        $ret = [];
        $what =  $this->getMyData();
        if (isset($what['bound'])) {
            $ret['bound'] = Schedule::from($what['bound']);
        }
        return $ret;
    }

}

