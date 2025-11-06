<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\ElementData;
use App\Data\ApiParams\Data\Elements\Responses\ElementList;
use App\Data\ApiParams\Data\Sets\Params\AddElementsParamData;
use App\Helpers\Utilities;
use App\Models\ElementSet;
use App\Models\ElementSetMember;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

#[ApiParamMarker( param_class: AddElementsParamData::class)]
class AddElement extends Api\SetApi
{
    const UUID = 'be4df284-6dc0-4cba-b607-2cf6de540d87';
    const TYPE_NAME = 'api_set_add_element';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\St\SetMemberAdd::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Utilities::ignoreVar($command_args);
        Log::debug("Called api add element to set node");
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public static function addElementsToSet(
        AddElementsParamData $params,
        UserNamespace $calling_namespace,ElementSet $given_set,bool $is_system,
        array $tags = [], ?IThangBuilder $builder = null
    ) : ElementList|Thang|Collection
    {


        $my_command = CommandParams::validateAndCreate([
            'command_class' => static::class,
            'command_tags' => array_merge([static::class], $tags)
        ]);

        ($builder ?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($calling_namespace)
            ->setSharedArg('namespace', $calling_namespace)
            ->tree($my_command);



        Act\Cmd\St\SetMemberAdd::createSetAddTree(params: $params, given_set: $given_set,
            is_system: $is_system, calling_namespace: $calling_namespace, builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            /** @var Collection<ElementSetMember> $members */
            $members = $data['members'];
            return ElementData::collect($members, Collection::class);
        } else {
            return $thang;
        }

    }


}

