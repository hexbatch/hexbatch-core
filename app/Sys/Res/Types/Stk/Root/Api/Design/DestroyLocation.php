<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Helpers\Utilities;
use App\Models\LocationBound;
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

class DestroyLocation extends Api\DesignApi implements ICommandCallable
{
    const UUID = '375b019a-399e-420b-b48c-747c3319115e';
    const TYPE_NAME = 'api_design_location_destroy';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api destroy location node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /** @throws \Throwable */
    public static function destroyLocation(UserNamespace $namespace,LocationBound $bound, array $tags = [], ?IThangBuilder $builder = null)
    : null|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['destroy-location'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($namespace)
            ->setSharedArg('namespace',$namespace)
            ->setSharedArg('given_bound',$bound)
            ->tree($my_command)
            ->leaf([
                'command_class' =>Act\Cmd\Ds\DesignLocationDestroy::class,
                'command_args' =>[
                    'namespace_uuid'=>Utilities::getCurrentNamespace()->ref_uuid,
                    'bound_uuid'=>$bound->ref_uuid
                ],
                'command_tags' =>[Act\Cmd\Ds\DesignLocationDestroy::class]
            ]);

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return null;
        } else {
            return $thang;
        }

    }

}

