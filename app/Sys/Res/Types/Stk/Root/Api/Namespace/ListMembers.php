<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Annotations\ApiParamMarker;

use App\Data\ApiParams\Data\Namespaces\NamespaceMemberData;
use App\Data\ApiParams\Data\Namespaces\Params\ListMembersParamData;
use App\Helpers\Utilities;
use App\Models\UserNamespace;
use App\Models\UserNamespaceMember;
use App\Sys\Res\Types\Stk\Root\Api;
use Spatie\LaravelData\CursorPaginatedDataCollection;


#[ApiParamMarker( param_class: ListMembersParamData::class)]
class ListMembers extends Api\NamespaceApi
{
    const UUID = '28300dbe-984c-4084-ab4d-f7c7dfa529d9';
    const TYPE_NAME = 'api_namespace_list_members';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
    ];

    /**
     * @return CursorPaginatedDataCollection<NamespaceMemberData>
     */
    public static function listMembers(ListMembersParamData $params, UserNamespace $caller_namespace, UserNamespace $target_namespace) {

        Utilities::ignoreVar($caller_namespace);
        $build = UserNamespaceMember::buildGroupMembers(
            namespace_parent_id: $target_namespace->id
        )->orderBy('created_at');
        $cursor = $build->cursorPaginate(perPage: config('hbc.pagination.default_page_size'), cursor: $params->cursor);
        return NamespaceMemberData::collect($cursor, CursorPaginatedDataCollection::class);
    }

}

