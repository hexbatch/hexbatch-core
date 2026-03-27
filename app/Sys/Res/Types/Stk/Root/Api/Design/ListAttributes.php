<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;

use App\Data\ApiParams\Data\Attributes\AttributeData;
use App\Data\ApiParams\Data\Attributes\Params\AttributeSearchParams;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\LocationBound;
use App\Models\UserNamespace;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use App\Sys\Res\Types\Stk\Root\Api;


#[ApiParamMarker( param_class: AttributeSearchParams::class)]
class ListAttributes extends Api\DesignApi
{
    const UUID = '293ec496-e455-4dbe-8058-c6b528370268';
    const TYPE_NAME = 'api_design_list_attributes';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
    ];

    public function __construct(
        protected ?AttributeSearchParams $params = null,

        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init,
            is_async: $this->is_async,tags: $this->tags);
    }





    /**
     * @return CursorPaginatedDataCollection<ActionDatum>
     */
    public static function listAttributes(AttributeSearchParams $params,UserNamespace $caller_namespace) {

        $namespace_id = null;
        if ($params->namespace_uuid) {
            $namespace_id = UserNamespace::resolveNamespace(value: $params->namespace_uuid)->id;
        }

        $parent_id = null;
        if ($params->parent_uuid) {
            $parent_id = Attribute::resolveAttribute(value: $params->parent_uuid)->id;
        }

        $type_id = null;
        if ($params->type_uuid) {
            $type_id = ElementType::resolveType(value: $params->type_uuid)->id;
        }

        $shape_id = null;
        if ($params->location_uuid) {
            $shape_id = LocationBound::resolveLocation(value: $params->location_uuid)->id;
        }

        $design_id = null;
        if ($params->design_uuid) {
            $design_id = ElementType::resolveType(value: $params->design_uuid)->id;
        }


        $build = Attribute::buildAttribute(
            namespace_id: $namespace_id,
            member_of_namespace_id: $caller_namespace->id,
            parent_id: $parent_id,
            type_id: $type_id,
            shape_id: $shape_id,
            design_id: $design_id,
            is_system: $params->is_system,
            b_do_relations: true,
            name: $params->attribute_name
        )->orderBy('created_at');

        $cursor = $build->cursorPaginate(perPage: 15, cursor: $params->cursor);
        return AttributeData::collect($cursor, CursorPaginatedDataCollection::class);
    }

}

