<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Data\ApiParams\Data\Elements\Responses\ElementList;
use App\Models\Phase;
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
use Spatie\LaravelData\CursorPaginatedDataCollection;

#[ApiParamMarker( param_class: SelectElementParamData::class)]
class ChangePhase extends Api\ElementApi implements ICommandCallable
{
    const UUID = '4d95fc86-a5ae-4c08-8d73-6d1810263c62';
    const TYPE_NAME = 'api_element_change_phase';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\ElementOwnerChange::class,
    ];



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api change phase node");
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function doElementChangePhase(
        SelectElementParamData    $params,
        Phase                     $given_phase,
        UserNamespace             $calling_namespace,
        bool                      $is_system,

        array $tags = [], ?IThangBuilder $builder = null
    ) : ElementList|Thang|CursorPaginatedDataCollection
    {


        $my_command = CommandParams::validateAndCreate([
            'command_class' => static::class,
            'command_tags' => array_merge([static::class], $tags)
        ]);
        ($builder ?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace', $calling_namespace)
            ->tree($my_command);


        Act\Cmd\Ele\ElementPhaseChange::changeElementPhaseTree(
            params: $params,
            given_phase: $given_phase, is_system: $is_system, calling_namespace: $calling_namespace,
            builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return static::rebuildElementList(data: $thang->finished_data,key: 'elements',length: static::CURSOR_ALL_LENGTH);
        } else {
            return $thang;
        }

    }



}

