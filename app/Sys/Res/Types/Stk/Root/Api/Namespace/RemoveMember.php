<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Namespaces\Params\NamespaceSelectionParamData;
use App\Data\ApiParams\Data\Namespaces\Responses\NamespaceMemberListData;
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

#[ApiParamMarker( param_class: NamespaceSelectionParamData::class)]
class RemoveMember extends Api\NamespaceApi implements ICommandCallable
{
    const UUID = '310b7928-d5a6-4fcd-9ab7-3ca85d932408';
    const TYPE_NAME = 'api_namespace_remove_member';



    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\NamespaceMemberRemove::class,
    ];

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api remove member");

        $b_approved = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: $children_args['results']);
    }

    /**
     * @throws \Throwable
     */
    public static function doRemoveMember(NamespaceSelectionParamData $params,
                                         UserNamespace $calling_namespace,UserNamespace $target_namespace,
                                         bool $do_permission_check, array $tags = [], ?IThangBuilder $builder = null)
    : NamespaceMemberListData|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['remove-member'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ns\NamespaceMemberRemove::makeTree(
            params: $params,
            calling_namespace: $calling_namespace,
            target_namespace: $target_namespace,
            do_permission_check: $do_permission_check,builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return NamespaceMemberListData::from($thang->finished_data);
        } else {
            return $thang;
        }

    }

}

