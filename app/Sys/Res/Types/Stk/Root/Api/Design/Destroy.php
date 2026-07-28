<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Models\ElementType;
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


class Destroy extends Api\DesignApi implements ICommandCallable
{
    const UUID = '74ff2b6e-4b93-4db1-b8fe-c3eb672cc16b';
    const TYPE_NAME = 'api_design_destroy';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignDestroy::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api destroy type node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function destroyDesign(
        UserNamespace $namespace,ElementType $given_type,bool $do_permission_check, array $tags = [], ?IThangBuilder $builder = null
    ) : ElementType|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['destroy-design'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($namespace)
            ->setSharedArg('namespace',$namespace)
            ->tree($my_command)
            ->leaf(
                command_class: Act\Cmd\Ds\DesignDestroy::class,
                command_args: new Act\Cmd\Ds\DesignDestroy(
                    given_type: $given_type,
                    caller_namespace: $namespace,
                    do_permission_check: $do_permission_check
                )->toArray(),
                command_tags: [Act\Cmd\Ds\DesignDestroy::class]
            );

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            /** @var ElementType $data */
            $data = $thang->finished_data;
            return  $data;
        } else {
            return $thang;
        }

    }



}

