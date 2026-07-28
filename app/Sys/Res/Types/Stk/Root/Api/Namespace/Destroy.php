<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Namespaces\Params\DeleteNamespacesParamData;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;


#[ApiParamMarker( param_class: DeleteNamespacesParamData::class)]
class Destroy extends Api\NamespaceApi
{
    const UUID = '29699a32-a22d-44b8-9525-91d27f9fc33b';
    const TYPE_NAME = 'api_namespace_destroy';


    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\NamespaceDestroy::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api ns delete");

        $b_approved = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['children_args'=>$children_args,static::CHILD_DECISION_KEY=>$b_approved]);
    }

    /**
     * @throws \Throwable
     */
    public static function doDeletion(DeleteNamespacesParamData $params,
                                           UserNamespace      $calling_namespace,UserNamespace      $target_namespace,
                                            bool $do_permission_check = true,
                                           array $tags = [], ?IThangBuilder $builder = null)
    : DeleteNamespacesParamData|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['read'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ns\NamespaceDestroy::doDeletion(
            params: $params,
            calling_namespace: $calling_namespace,target_namespace: $target_namespace,do_permission_check: $do_permission_check,builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return DeleteNamespacesParamData::from($thang->finished_data['children_args']);
        } else {
            return $thang;
        }

    }

}

