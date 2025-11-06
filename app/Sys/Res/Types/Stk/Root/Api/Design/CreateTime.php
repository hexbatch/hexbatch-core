<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Schedules\Schedule;
use App\Helpers\Utilities;

use App\Models\TimeBound;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;

use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;

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
            /** @var TimeBound $time_bound|null $time_bound */
            $time_bound = TimeBound::buildTimeBound(uuid: $data['ref_uuid'],with_spans: true)->first();
            return $time_bound;
        } else {
            return $thang;
        }

    }

}

