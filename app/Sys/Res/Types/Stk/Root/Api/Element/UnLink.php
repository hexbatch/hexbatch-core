<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Data\ApiParams\Data\Elements\Responses\ElementList;
use App\Models\ElementSet;
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
class UnLink extends Api\ElementApi implements ICommandCallable
{
    const UUID = '7dddcc46-b3dc-464a-8088-425c44c5b993';
    const TYPE_NAME = 'api_element_unlink';

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\LinkRemove::class,
    ];

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api link element");
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function doRemoveLink(
        SelectElementParamData    $params,
        ElementSet                     $given_set,
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


        Act\Cmd\Ele\LinkRemove::linkRemoveTree(
            params: $params,
            given_set: $given_set, is_system: $is_system, calling_namespace: $calling_namespace,
            builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return static::rebuildElementList(data: $thang->finished_data,key: 'elements',length: static::CURSOR_ALL_LENGTH);
        } else {
            return $thang;
        }

    }

}

