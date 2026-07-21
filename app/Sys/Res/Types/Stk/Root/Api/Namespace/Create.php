<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Namespaces\Params\NamespaceParamData;
use App\Data\ApiParams\Data\Namespaces\UserNamespaceData;
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

#[ApiParamMarker( param_class: NamespaceParamData::class)]
class Create extends Api\NamespaceApi implements ICommandCallable
{
    const UUID = 'ba68ba5e-37c5-45bd-9ca5-eadf0c798ef4';
    const TYPE_NAME = 'api_namespace_create';


    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\NamespaceCreate::class,
    ];

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api create namespace");

        $b_approved = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['children_args'=>$children_args,static::CHILD_DECISION_KEY=>$b_approved,'namespace'=>$children_args['namespace']]);
    }

    /**
     * @throws \Throwable
     */
    public static function doNamespaceCreate(NamespaceParamData $params,UserNamespace      $calling_namespace,
                                        bool $is_system, array $tags = [], ?IThangBuilder $builder = null)
    : UserNamespaceData|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['read'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ns\NamespaceCreate::makeCreateNamespaceTree(
            params: $params,
            given_user: null,given_server: null,
            is_system: $is_system,
            calling_namespace: $calling_namespace,builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return UserNamespaceData::from($thang->finished_data);
        } else {
            return $thang;
        }

    }

}

