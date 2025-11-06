<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\Params\WriteElementParamData;
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


#[ApiParamMarker( param_class: WriteElementParamData::class)]
class WriteAttribute extends Api\ElementApi implements ICommandCallable
{
    const UUID = '26a090a2-708a-4c76-b387-08f537f0c2d5';
    const TYPE_NAME = 'api_element_write';


    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Write::class,
    ];



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api write attribute node");

        $b_approved = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['children_args'=>$children_args,static::CHILD_DECISION_KEY=>$b_approved]);
    }

    /**
     * @throws \Throwable
     */
    public static function write(WriteElementParamData $params,
                                        UserNamespace $calling_namespace,
                                        bool $is_system, array $tags = [], ?IThangBuilder $builder = null)
    : true|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['write'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ele\Write::createWriteTree(
            params: $params,
            is_system: $is_system,
            calling_namespace: $calling_namespace,builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return true;
        } else {
            return $thang;
        }

    }


}

