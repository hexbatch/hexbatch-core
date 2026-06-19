<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\ElementData;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
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


#[ApiParamMarker( param_class: SelectElementParamData::class)]
class SwitchOff extends Api\ElementApi implements ICommandCallable
{
    const UUID = '2a8f43d7-62b1-4776-9868-42a31de9035d';
    const TYPE_NAME = 'api_element_type_off';

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\SwitchOff::class,
    ];

    const ACTION_CLASS = Act\Cmd\Ele\SwitchOff::class;
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api Switch off");
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data: $children_args);
    }


    /**
     * @throws \Throwable
     */
    public static function doSwitch(
        UserNamespace $calling_namespace,bool $is_system, SelectElementParamData $params,
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


        static::ACTION_CLASS::createSwitchTree(params: $params,
            is_system: $is_system,
            calling_namespace: $calling_namespace,
            builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;

            /** @var Collection $elements */
            $elements = $data['elements']??null;
            $refs = [];
            foreach ($elements as $el) {
                $refs[] = $el->ref_uuid;
            }
            $build = Element::buildElement(
                given_uuids: $refs
            )->orderBy('id');

            $cursor = $build->cursorPaginate(perPage: $elements->count());

            return ElementData::collect($cursor, CursorPaginatedDataCollection::class);
        } else {
            return $thang;
        }

    }

}

