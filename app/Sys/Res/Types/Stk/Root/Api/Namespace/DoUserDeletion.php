<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Data\ApiParams\Data\Namespaces\Params\ChangeNamespacesParamData;
use App\Models\User;
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


class DoUserDeletion extends Api\UserApi implements ICommandCallable
{
    const UUID = '2bf19367-618b-4ef4-8b56-00b2e6717f7d';
    const TYPE_NAME = 'api_do_user_deletion';


    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\UserDeleteDo::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api delete user");

        $b_approved = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['children_args'=>$children_args,static::CHILD_DECISION_KEY=>$b_approved]);
    }

    /**
     * @throws \Throwable
     */
    public static function doUserDeletion(ChangeNamespacesParamData $params,
                                           UserNamespace             $calling_namespace, UserNamespace $target_namespace,
                                           User                     $target_user,
                                           bool                      $do_permission_check = true,
                                           array                     $tags = [], ?IThangBuilder $builder = null)
    : ChangeNamespacesParamData|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['destroy-ns'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ns\UserDeleteDo::makeTree(
            params: $params,
            calling_namespace: $calling_namespace,target_namespace: $target_namespace,target_user:$target_user,do_permission_check: $do_permission_check,builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return ChangeNamespacesParamData::from($thang->finished_data['children_args']);
        } else {
            return $thang;
        }

    }

}

