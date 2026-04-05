<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Schedules\Schedule;
use App\Enums\Sys\TypeOfAction;
use App\Models\TimeBound;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
class DesignTimeCreate extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = '777c5080-dc81-40f8-8017-1a3a8a831a07';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_TIME_CREATE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];



    /** @throws \Throwable */
    protected static function createScheduleBound(Schedule $params, UserNamespace $namespace, ?TimeBound $given_bound = null) : TimeBound
    {
        if ($given_bound) {
            static::checkIfGivenIsAdmin(given: $namespace,target: $given_bound->schedule_namespace);
        }
        DB::transaction(function () use($params,$namespace,&$given_bound){
            $collect = new Collection(
                [
                    'bound_name' => $params->bound_name,
                    'bound_start' => $params->bound_start,
                    'bound_stop' => $params->bound_stop,
                    'bound_cron' => $params->bound_cron,
                    'bound_cron_timezone' => $params->bound_cron_timezone,
                    'bound_period_length' => $params->bound_period_length,
                ]
            );
            if ($given_bound) {
                $given_bound = TimeBound::collectTimeBound(collect: $collect,bound: $given_bound);
            } else {
                $given_bound = TimeBound::collectTimeBound(collect: $collect,namespace: $namespace);
            }
        });
        return $given_bound;
    }

    /** @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $params = Schedule::validateAndCreate($command_args['schedule_params']);
        $namespace = $command_args['namespace'];
        $bound = $command_args['given_bound']??null;
        $new_schedule = static::createScheduleBound(params: $params,namespace: $namespace,given_bound: $bound);
        Log::debug("Called design time create node",['args'=>$command_args,'schedule'=>$new_schedule]);
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $new_schedule->toArray());
    }

}

