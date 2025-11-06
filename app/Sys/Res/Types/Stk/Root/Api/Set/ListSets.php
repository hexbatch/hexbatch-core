<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Data\ApiParams\Data\Sets\Responses\SetList;
use App\Data\ApiParams\Data\Sets\SetData;
use App\Helpers\Utilities;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Pagination\CursorPaginator;
use Spatie\LaravelData\CursorPaginatedDataCollection;

#[ApiParamMarker( param_class: SelectElementParamData::class)]
class ListSets extends Api\SetApi
{
    const UUID = 'bd6a0fef-b3bf-4f33-8988-6714ff385d71';
    const TYPE_NAME = 'api_set_sets';


    const PARENT_CLASSES = [
        Api\SetApi::class
    ];



    public static function listSets(SelectElementParamData $params,UserNamespace $caller_namespace)
    : SetList|Thang|CursorPaginator
    {
        Utilities::ignoreVar($caller_namespace);

        $definer_ids = Element::getBuilderFromParams(
            params: $params, b_ns_relations: false, b_type_relations: false, b_ns_type_relations: false)
            ->limit(config('hbc.pagination.default_element_limit'))->pluck('elements.id')->toArray();

        $set_builder = ElementSet::buildSet(defining_element_ids:$definer_ids)
            ->with('defining_element','defining_type');
        $cursor = $set_builder->cursorPaginate(perPage: config('hbc.pagination.default_element_limit'), cursor: $params->cursor);
        return SetData::collect($cursor, CursorPaginatedDataCollection::class);
    }



}

