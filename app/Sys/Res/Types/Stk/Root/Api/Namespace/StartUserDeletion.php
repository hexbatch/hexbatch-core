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


class StartUserDeletion extends Api\NamespaceApi implements ICommandCallable
{
    const UUID = '910756fc-4964-452f-9360-cfed59bd6938';
    const TYPE_NAME = 'api_user_prepare_deletion';


    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\UserStartDeletion::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api pre user deletion");

        $b_approved = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['children_args'=>$children_args,static::CHILD_DECISION_KEY=>$b_approved]);
    }

    /**
     * @throws \Throwable
     */
    public static function doStartUserDeletion(ChangeNamespacesParamData $params,
                                           UserNamespace             $calling_namespace, UserNamespace $target_namespace,
                                           User $target_user,
                                           array                     $tags = [], ?IThangBuilder $builder = null)
    : ChangeNamespacesParamData|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['start-transfer'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ns\UserStartDeletion::makeTree(
            params: $params,
            calling_namespace: $calling_namespace,target_namespace: $target_namespace,target_user: $target_user,builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return ChangeNamespacesParamData::from($thang->finished_data['children_args']);
        } else {
            return $thang;
        }

    }

}

