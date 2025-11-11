<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;



use App\Data\ApiParams\Data\Schedules\Schedule;
use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\TimeBound;
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


class DestroyTime extends Api\DesignApi implements ICommandCallable
{
    const UUID = 'd55e0d09-0830-4723-acbc-acb3595b7d57';
    const TYPE_NAME = 'api_design_destroy_time';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignTimeDestroy::class,
    ];

    public function __construct(
        protected TimeBound $bound,

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
        return ['bound'=>$this->bound];
    }

    public function getDataSnapshot(): Schedule
    {
        $what =  $this->getMyData();
        return Schedule::validateAndCreate($what['bound']->toArray());
    }


    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignTimeDestroy(
            given_time_uuid: $this->bound->ref_uuid,
             tags: ['edit time bound from api']);
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

        if ($child instanceof Act\Cmd\Ds\DesignTimeDestroy) {
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
        Log::debug("Called api destroy time node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /** @throws \Throwable */
    public static function destroySchedule(TimeBound $bound, array $tags = [], ?IThangBuilder $builder = null)
    : array|null|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['destroy-schedule'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->tree($my_command)
            ->leaf([
                'command_class' =>Act\Cmd\Ds\DesignTimeCreate::class,
                'command_args' =>[
                    'namespace'=>Utilities::getCurrentNamespace(),
                    'given_bound'=>$bound
                ],
                'command_tags' =>[Act\Cmd\Ds\DesignTimeDestroy::class]
            ]);

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return null;
        } else {
            return $thang;
        }

    }
}

