<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Namespaces\Params\ListNamespacesParamData;
use App\Data\ApiParams\Data\Namespaces\UserNamespaceData;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Api;
use Spatie\LaravelData\CursorPaginatedDataCollection;

#[ApiParamMarker( param_class: ListNamespacesParamData::class)]
class ListNamespaces extends Api\NamespaceApi
{
    const UUID = '52fbdcdd-1d64-4ea5-90c4-e68d03df825c';
    const TYPE_NAME = 'api_namespace_list';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
    ];

    /**
     * @return CursorPaginatedDataCollection<UserNamespaceData>
     */
    public static function listNamespaces(ListNamespacesParamData $params, UserNamespace $caller_namespace) {


        $build = UserNamespace::buildNamespace(
            id_is_member_of_namespace: $params->is_member? $caller_namespace->id: null,
            id_is_admin_of_namespace: $params->is_admin? $caller_namespace->id: null,
            link_uuid: $params->link_uuid?: null,
            base_type_handle_uuid: $params->base_handle_uuid?: null,
        )->orderBy('namespace_name');
        $cursor = $build->cursorPaginate(perPage: config('hbc.pagination.default_page_size'), cursor: $params->cursor);
        return UserNamespaceData::collect($cursor, CursorPaginatedDataCollection::class);
    }

}

