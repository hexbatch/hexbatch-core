<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

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


class Publish extends Api\TypeApi implements ICommandCallable
{
    const UUID = '81c04881-39a5-4903-aaf2-34633b6f4f69';
    const TYPE_NAME = 'api_type_publish';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ty\TypePublish::class
    ];




    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api publish node");
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function doPublish(
        UserNamespace $calling_namespace,ElementType $given_type,bool $do_permission_check,
        array $tags = [], ?IThangBuilder $builder = null
    ) : ElementType|Thang
    {


        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge([static::class],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command)
        ;



        Act\Cmd\Ty\TypePublish::publish(calling_namespace: $calling_namespace,given_type: $given_type,
            do_permission_check: $do_permission_check,builder: $builder);



        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  ElementType::getElementType(uuid: $data['ref_uuid']);
        } else {
            return $thang;
        }

    }

}

