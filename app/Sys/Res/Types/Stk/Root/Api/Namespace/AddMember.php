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
class AddMember extends Api\NamespaceApi implements ICommandCallable
{
    const UUID = '265a64ec-f02c-44f1-9474-80c50e4f737b';
    const TYPE_NAME = 'api_namespace_add_member';

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\NamespaceMemberAdd::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api add member");

        $b_approved = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: $children_args['results']);
    }

    /**
     * @throws \Throwable
     */
    public static function doAddMember(NamespaceSelectionParamData $params,
                                      UserNamespace $calling_namespace,UserNamespace $target_namespace,
                                      bool $do_permission_check, array $tags = [], ?IThangBuilder $builder = null)
    : NamespaceMemberListData|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['add-member'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ns\NamespaceMemberAdd::makeTree(
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

