<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Data\ApiParams\Data\Elements\Responses\ElementList;
use App\Data\ApiParams\Data\Sets\SetData;
use App\Helpers\Utilities;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Thangs\Models\Thang;
use Spatie\LaravelData\CursorPaginatedDataCollection;


#[ApiParamMarker( param_class: SelectElementParamData::class)]
class ShowSet extends Api\SetApi
{
    const UUID = 'b71a08ad-ca4f-40fa-9aac-9973a45cb44d';
    const TYPE_NAME = 'api_set_show';

    const PARENT_CLASSES = [
        Api\SetApi::class
    ];





    public static function showSet(ElementSet $set,SelectElementParamData $params,UserNamespace $caller_namespace)
    : SetData|Thang
    {
        Utilities::ignoreVar($caller_namespace);
        $params->set_ref = $set->ref_uuid;
        $params->phase_ref = $set->defining_element->element_phase->ref_uuid;
        $build = Element::getBuilderFromParams(
            params: $params, b_ns_relations: true, b_type_relations: true, b_ns_type_relations: true);

        $members_paginated = $build->cursorPaginate(perPage: config('hbc.pagination.default_element_limit'), cursor: $params->cursor);
        $members = ElementList::collect($members_paginated, CursorPaginatedDataCollection::class);
        $set->loadMissing(['defining_element','defining_type','parent_set','children_sets']);
        $ret =  SetData::from($set);
        $ret->element_members = $members;
        return $ret;
    }

}

