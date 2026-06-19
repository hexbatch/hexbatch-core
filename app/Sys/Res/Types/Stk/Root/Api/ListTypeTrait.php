<?php

namespace App\Sys\Res\Types\Stk\Root\Api;

use App\Data\ApiParams\Data\Types\ElementTypeData;
use App\Data\ApiParams\Data\Types\Params\TypeSearchParams;
use App\Enums\Types\TypeOfLifecycle;
use App\Models\ElementType;
use App\Models\UserNamespace;
use Spatie\LaravelData\CursorPaginatedDataCollection;

trait ListTypeTrait
{
    /**
     * @return CursorPaginatedDataCollection<ElementTypeData>
     */
    public static function listCursoratedTypes(UserNamespace $calling_namespace,?TypeSearchParams $params,?TypeOfLifecycle $lifecycle = null) {

        if ($params?->namespace_uuid??null) {
            $scope_namespace = UserNamespace::resolveNamespace(value: $params->namespace_uuid);
            //see if caller is a member of scope
            static::checkIfGivenIsMember(given: $calling_namespace,target: $scope_namespace);
            $namespace_id = $scope_namespace->id;
        } else {
            $namespace_id = $calling_namespace;
        }

        $location_id = null;
        if ($params?->location_uuid??null) {
            $location_id = UserNamespace::resolveNamespace(value: $params->namespace_uuid)->id;
        }

        $schedule_id = null;
        if ($params?->schedule_uuid??null) {
            $schedule_id = UserNamespace::resolveNamespace(value: $params->namespace_uuid)->id;
        }

        $handle_id = null;
        if ($params?->handle_uuid??null) {
            $handle_id = UserNamespace::resolveNamespace(value: $params->namespace_uuid)->id;
        }

        $is_system = null;
        if ($params?->is_system??null) {
            $is_system = $params->is_system;
        }


        $build = ElementType::buildElementType(
            namespace_id: $namespace_id,
            name: $params?->type_name,
            shape_bound_id: $location_id,
            time_bound_id: $schedule_id,
            handle_id: $handle_id,
            is_system: $is_system,
            lifecycle: $lifecycle
        )->orderBy('type_name');
        $cursor = $build->cursorPaginate(perPage: 15, cursor: $params->cursor);
        return ElementTypeData::collect($cursor, CursorPaginatedDataCollection::class);
    }
}
