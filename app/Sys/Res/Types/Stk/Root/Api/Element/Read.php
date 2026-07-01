<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\Params\ReadElementParamData;
use App\Data\ApiParams\Data\Elements\Responses\ElementReadingList;
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


#[ApiParamMarker( param_class: ReadElementParamData::class)]
class Read extends Api\ElementApi implements ICommandCallable
{
    const UUID = 'ae6b7b0e-8991-4443-9f00-3e9a637a52ce';
    const TYPE_NAME = 'api_element_read';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Read::class,
    ];







    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api create attribute node");

        $b_approved = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['children_args'=>$children_args,static::CHILD_DECISION_KEY=>$b_approved]);
    }

    /**
     * @throws \Throwable
     */
    public static function readElements(ReadElementParamData $params,UserNamespace      $calling_namespace,
                                           bool $is_system, array $tags = [], ?IThangBuilder $builder = null)
    : ElementReadingList|Thang
    {

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['read'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace',$calling_namespace)
            ->tree($my_command);

        Act\Cmd\Ele\Read::createReadTree(
            params: $params,is_system: $is_system,
            calling_namespace: $calling_namespace,builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return ElementReadingList::from($thang->finished_data);
        } else {
            return $thang;
        }

    }

}

