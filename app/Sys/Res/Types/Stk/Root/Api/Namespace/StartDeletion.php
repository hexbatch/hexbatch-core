<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Namespaces\Params\ChangeNamespacesParamData;
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

#[ApiParamMarker( param_class: ChangeNamespacesParamData::class)]
class StartDeletion extends Api\NamespaceApi implements ICommandCallable
{
    const UUID = '49ec661b-610b-47bd-a427-b7ea0e61422d';
    const TYPE_NAME = 'api_start_deletion';


    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\NamespaceCreate::class,
    ];

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api pre delete");

        $b_approved = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['children_args'=>$children_args,static::CHILD_DECISION_KEY=>$b_approved]);
    }

    /**
     * @throws \Throwable
     */
    public static function doStartDeletion(ChangeNamespacesParamData $params,
                                           UserNamespace             $calling_namespace, UserNamespace $target_namespace,
                                           array                     $tags = [], ?IThangBuilder $builder = null)
    : ChangeNamespacesParamData|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['start-deletion'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ns\NamespaceDeletionStart::makeTree(
            params: $params,
            calling_namespace: $calling_namespace,target_namespace: $target_namespace,builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return ChangeNamespacesParamData::from($thang->finished_data['children_args']);
        } else {
            return $thang;
        }

    }

}

