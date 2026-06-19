<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;



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


class DestroyTime extends Api\DesignApi implements ICommandCallable
{
    const UUID = 'd55e0d09-0830-4723-acbc-acb3595b7d57';
    const TYPE_NAME = 'api_design_destroy_time';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignTimeDestroy::class,
    ];




    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api destroy time node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /** @throws \Throwable */
    public static function destroySchedule(UserNamespace $namespace,TimeBound $bound, array $tags = [], ?IThangBuilder $builder = null)
    : null|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['destroy-schedule'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($namespace)
            ->setSharedArg('namespace',$namespace)
            ->setSharedArg('given_bound',$bound)
            ->tree($my_command)
            ->leaf([
                'command_class' =>Act\Cmd\Ds\DesignTimeDestroy::class,
                'command_args' =>[
                    'namespace_uuid'=>Utilities::getCurrentNamespace()->ref_uuid,
                    'bound_uuid'=>$bound->ref_uuid
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

