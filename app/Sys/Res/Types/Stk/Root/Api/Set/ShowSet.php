<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\ElementData;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Data\ApiParams\Data\Sets\SetData;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementValue;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Collection;
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

        $set->loadMissing(['defining_element']);
        $set->loadMissing(['parent_set']);
        $set->loadMissing(['children_sets']);
        $set->loadMissing(['defining_type']);

        $params->set_ref = $set->ref_uuid;
        $params->phase_ref = $set->defining_element->element_phase->ref_uuid;
        $build = Element::getBuilderFromParams(
            params: $params, b_ns_relations: true, b_type_relations: true, b_ns_type_relations: false);

        /** @var Collection<Element> $members_paginated */
        $members_paginated = $build->cursorPaginate(perPage: config('hbc.pagination.default_element_limit'), cursor: $params->cursor);



        $ret =  SetData::from($set);

        $el_ids = [];
        /** @var array<Element> $element_by_ref */
        $element_by_ref = [];
        foreach ($members_paginated as $mem) {
            $el_ids[] = $mem->id;
            $element_by_ref[$mem->ref_uuid] = $mem;
        }



        if (count($el_ids)) {
            $values = ElementValue::readValues(set_id: $set->id,element_ids: $el_ids,caller_namespace_id: $caller_namespace->id );
            $el_readings = [];
            foreach ($values->data as $val) {
                if (!isset($el_readings[$val->element_uuid])) {
                    $el_readings[$val->element_uuid] = [];
                }
                $el_readings[$val->element_uuid][] = $val;
            }
        }
        foreach ($element_by_ref as $el_ref => $da_el) {
            if (isset($el_readings[$el_ref]) && count($el_readings[$el_ref])) {
                $da_el->data_values = $el_readings[$el_ref];
            }
        }

        $members = ElementData::collect($members_paginated, CursorPaginatedDataCollection::class);
        $ret->element_members = $members;
        return $ret;
    }

}

