<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Annotations\ApiParamMarker;

use App\Data\ApiParams\Data\Sets\Params\CreateSetParamData;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\SetCreate;
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


#[ApiParamMarker( param_class: CreateSetParamData::class)]
class CreateSet extends Api\SetApi implements ICommandCallable
{
    const UUID = '7255ea40-d9f7-40d3-87c8-442269c77c96';
    const TYPE_NAME = 'api_element_create_set';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\SetCreate::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api create set node");
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data: $children_args);
    }


    /**
     * @throws \Throwable
     */
    public static function doSetCreation(
        UserNamespace $calling_namespace,Element $defining_element,bool $is_system, CreateSetParamData $params,
        array $tags = [], ?IThangBuilder $builder = null
    ) : ElementSet|Thang
    {


        $my_command = CommandParams::validateAndCreate([
            'command_class' => static::class,
            'command_tags' => array_merge([static::class], $tags)
        ]);
        ($builder ?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace', $calling_namespace)
            ->tree($my_command);

        $parent_set = null;
        if ($params->parent_set_ref) {
            $parent_set = ElementSet::getThisSet(uuid: $params->parent_set_ref);
        }

        Act\Cmd\Ele\SetCreate::createSetTree(defining_element: $defining_element, has_events: $params->has_events,
            is_system: $is_system,
            calling_namespace: $calling_namespace,
            parent_set: $parent_set,   builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            /** @var array|ElementSet $set */
            $set = $data[SetCreate::SET_KEY_IN_ARGS]??null;
            if (is_array($set)) {
                $uuid = $data['ref_uuid'];
            } else {
                $uuid = $set->ref_uuid;
            }
            return  ElementSet::getThisSet(uuid: $uuid);
        } else {
            return $thang;
        }

    }

}

