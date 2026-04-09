<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Models\Element;
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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;


#[ApiParamMarker( param_class: SelectElementParamData::class)]
class Destroy extends Api\ElementApi implements ICommandCallable
{
    const UUID = 'bd9d7481-5f47-4bd6-8ec0-90f4df0c91be';
    const TYPE_NAME = 'api_element_destroy';



    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\ElementDestroy::class,
    ];



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        Log::debug("Called api destroy element api type node");
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data: $children_args);

    }

    /**
     * @throws \Throwable
     * @return Collection<Element>|Thang
     */
    public static function destroyElements(
        SelectElementParamData $params, bool $is_system,
        UserNamespace $caller_namespace,
        array $tags = [], ?IThangBuilder $builder = null
    ) : Collection|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['destroy-elements'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($caller_namespace)
            ->setSharedArg('namespace',$caller_namespace)
            ->tree($my_command);


        Act\Cmd\Ele\ElementDestroy::destroyElements(
            params: $params,
            is_system: $is_system,
            caller_namespace: $caller_namespace,
            builder: $builder
        );



        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            /** @var Collection<Element> $data */
            $data = $thang->finished_data;
            return  $data;
        } else {
            return $thang;
        }

    }



}

