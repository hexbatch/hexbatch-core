<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Data\ApiParams\Data\Elements\ElementData;
use App\Data\ApiParams\Data\Elements\Responses\ElementList;
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
use Spatie\LaravelData\CursorPaginatedDataCollection;


class ChangeOwner extends Api\ElementApi implements ICommandCallable
{
    const UUID = '513a16a3-cbb5-4f6e-a6e4-4e7b90b0a1c6';
    const TYPE_NAME = 'api_element_change_owner';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\ElementOwnerChange::class,
    ];



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api create element node");
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function doElementChangeOwner(
        UserNamespace             $owner_namespace,
        UserNamespace             $calling_namespace,
        bool                      $is_system,

        /** @var Collection<Element>        $given_elements */
        Collection                $given_elements,
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


        Act\Cmd\Ele\ElementOwnerChange::changeElementOwnerTree(
            owner_namespace: $owner_namespace, is_system: $is_system, calling_namespace: $calling_namespace,
            given_elements: $given_elements, builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return static::rebuildElementList(data: $thang->finished_data,key: 'elements',length: static::CURSOR_ALL_LENGTH);
        } else {
            return $thang;
        }

    }



}

