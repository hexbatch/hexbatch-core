<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Data\ApiParams\Data\Live\LivePermissionData;
use App\Data\ApiParams\Data\Live\Params\LivePermissionParamData;
use App\Data\ApiParams\Data\Types\ElementTypeData;
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


class RemovePermission extends Api\TypeApi implements ICommandCallable
{
    const UUID = '9a121bcd-be56-442d-ba8a-fe1def45902b';
    const TYPE_NAME = 'api_type_remove_permission';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ty\LivePermissionRemove::class
    ];




    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api remove permission");
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function doRemovePermission(
        LivePermissionParamData $params,
        UserNamespace $calling_namespace,
        bool $do_permission_check,
        array $tags = [], ?IThangBuilder $builder = null
    ) : LivePermissionData|Thang
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



        Act\Cmd\Ty\LivePermissionRemove::removePermissionTree(params: $params,calling_namespace: $calling_namespace,
            do_permission_check: $do_permission_check,builder: $builder);



        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  LivePermissionData::makingUsingCodeArray($data['results'] ); //todo adjust
        } else {
            return $thang;
        }

    }

}

