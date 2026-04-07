<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\ElementData;
use App\Data\ApiParams\Data\Elements\Params\CreateElementParamData;
use App\Data\ApiParams\Data\Elements\Responses\ElementList;
use App\Models\Element;
use App\Models\ElementType;
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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\CursorPaginatedDataCollection;

/**
 *   if no set provided, it will put new element in the caller's home set.
 */
#[ApiParamMarker( param_class: CreateElementParamData::class)]
class CreateElement extends Api\ElementApi implements ICommandCallable
{
    const UUID = 'bad981d1-f817-4f89-879c-3d2d9c6443b6';
    const TYPE_NAME = 'api_types_create_element';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ty\ElementCreate::class,
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
    public static function doElementCreation(
        UserNamespace $calling_namespace,ElementType $given_type,bool $is_system,
        CreateElementParamData $params,
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
        if ($params->phase_ref) {
            $phase = Phase::getThisPhase(uuid: $params->phase_ref);
        } else {
            $phase = Phase::getDefaultPhase();
        }

        $owner_namespace = $calling_namespace;
        if ($params->namespace_ref && $owner_namespace->ref_uuid !== $params->namespace_ref) {
            $owner_namespace = UserNamespace::getThisNamespace(uuid: $params->namespace_ref);
        }

        $number_to_create = $params->number_to_create ?? 1;
        Act\Cmd\Ty\ElementCreate::createElementTree(element_type: $given_type, phase: $phase, number_to_create: $number_to_create,
            owner_namespace: $owner_namespace, is_system: $is_system, calling_namespace: $calling_namespace, builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            /** @var Collection<Element> $elements */
            $elements = $data['elements'];
            $refs = [];
            foreach ($elements as $el) {
                $refs[] = $el->ref_uuid;
            }
            $build = Element::buildElement(
                given_uuids: $refs
            )->orderBy('id')
            ;
            $cursor = $build->cursorPaginate(perPage: $number_to_create);
            return ElementData::collect($cursor, CursorPaginatedDataCollection::class);
        } else {
            return $thang;
        }

    }

}

