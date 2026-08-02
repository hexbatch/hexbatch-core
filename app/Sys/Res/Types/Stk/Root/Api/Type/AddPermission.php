<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Live\LivePermissionData;
use App\Data\ApiParams\Data\Live\Params\LivePermissionParamData;
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

#[ApiParamMarker( param_class: LivePermissionParamData::class)]
class AddPermission extends Api\TypeApi implements ICommandCallable
{
    const UUID = 'b24a16b1-7c32-4c05-bf98-ac6fe93b06c8';
    const TYPE_NAME = 'api_type_add_permission';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ty\LivePermissionAdd::class
    ];




    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api add permission node");
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function doAddPermission(
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



        Act\Cmd\Ty\LivePermissionAdd::addPermissionTree(params: $params,calling_namespace: $calling_namespace,
            do_permission_check: $do_permission_check,builder: $builder);



        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  LivePermissionData::makingUsingCodeArray($data['results'] );
        } else {
            return $thang;
        }

    }

}

