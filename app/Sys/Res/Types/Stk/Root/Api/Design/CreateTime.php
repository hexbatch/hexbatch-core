<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Schedules\Schedule;
use App\Helpers\Utilities;
use App\Models\ActionDatum;

use App\Models\TimeBound;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\Log;

#[ApiParamMarker( param_class: Schedule::class)]
class CreateTime extends Api\DesignApi implements ICommandCallable
{
    const UUID = 'b3b52738-f425-4083-9648-e777837696b7';
    const TYPE_NAME = 'api_design_create_time';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignTimeCreate::class,
    ];


    public function __construct(
        protected ?Schedule $params = null,

        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init,
            is_async: $this->is_async,tags: $this->tags);
    }


    protected function getMyData() :array {
        return ['bound'=>$this->getGivenTimeBound()];
    }

    public function getDataSnapshot(): Schedule
    {
        $what =  $this->getMyData();
        return Schedule::validateAndCreate($what['bound']->toArray());
    }






    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignTimeCreate(
            bound_name: $this->params->bound_name,
            bound_start: $this->params->bound_start,
            bound_stop: $this->params->bound_stop,
            bound_cron: $this->params->bound_cron,
            bound_cron_timezone: $this->params->bound_cron_timezone,
            bound_period_length: $this->params->bound_period_length,
            parent_action_data: $this->action_data,tags: ['create time bound from api']);
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1, 'title' => $creator->getType()->getName(),'action'=>$creator];


        //last in tree is the
        if (count($nodes)) {
            return new Tree(
                $nodes,
                ['rootId' => -1]
            );
        }
        return null;

    }


    /**
     * @throws \Exception
     */
    public function setChildActionResult(IThingAction $child): void {

        if ($child instanceof Act\Cmd\Ds\DesignTimeCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenTimeBound($child->getGivenTimeBound());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api create time node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /** @throws \Throwable */
    public static function makeSchedule(UserNamespace $namespace,?Schedule $params = null, array $tags = [], ?IThangBuilder $builder = null)
    : TimeBound|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['create-schedule'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($namespace)
            ->setSharedArg('namespace',$namespace)
            ->tree($my_command)
            ->leaf(
                command_class: Act\Cmd\Ds\DesignTimeCreate::class,
                command_args: [
                    'schedule_params'=>$params->toArray(),
                    'namespace_uuid'=>Utilities::getCurrentNamespace()->ref_uuid
                ],
                command_tags: [Act\Cmd\Ds\DesignTimeCreate::class]
            );

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            $time_bound = TimeBound::buildTimeBound(uuid: $data['ref_uuid'],with_spans: true)->first();
            return $time_bound;
        } else {
            return $thang;
        }

    }

}

