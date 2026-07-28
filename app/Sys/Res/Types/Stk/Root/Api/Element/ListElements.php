<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Elements\ElementData;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Helpers\Utilities;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Api;
use Spatie\LaravelData\CursorPaginatedDataCollection;

#[ApiParamMarker( param_class: SelectElementParamData::class)]
class ListElements extends Api\ElementApi
{
    const UUID = 'ec5c1437-ce47-4fcb-b8cf-88bb9dec9653';
    const TYPE_NAME = 'api_element_list';


    const PARENT_CLASSES = [
        Api\ElementApi::class
    ];


    /**
     * @return CursorPaginatedDataCollection<ElementData>
     */
    public static function listElements(SelectElementParamData $params,UserNamespace $caller_namespace) {
        Utilities::ignoreVar($caller_namespace);

        $build = Element::getBuilderFromParams(
            params: $params, b_ns_relations: true, b_type_relations: true, b_ns_type_relations: true,b_link_relations: true);



        $cursor = $build->cursorPaginate(perPage: config('hbc.pagination.default_element_limit'), cursor: $params->cursor);

        //todo add data
        return ElementData::collect($cursor, CursorPaginatedDataCollection::class);
    }

}

