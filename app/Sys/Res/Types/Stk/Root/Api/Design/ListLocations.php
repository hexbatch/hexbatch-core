<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Locations\Location;
use App\Data\ApiParams\Data\Locations\Params\LocationSearchParams;
use App\Helpers\Utilities;
use App\Models\LocationBound;
use App\Models\UserNamespace;

use App\Sys\Res\Types\Stk\Root\Api;
use Spatie\LaravelData\CursorPaginatedDataCollection;

#[ApiParamMarker( param_class: LocationSearchParams::class)]
class ListLocations extends Api\DesignApi
{
    const UUID = 'db5971de-fe4e-498e-b2a5-12990cdb2b26';
    const TYPE_NAME = 'api_design_list_locations';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
    ];


    /**
     * @return CursorPaginatedDataCollection<Location>
     */
    public static function listLocations(?LocationSearchParams $params) {

        if ($params?->namespace_ref) {
            $namespace_id = UserNamespace::resolveNamespace(value: $params->namespace_ref)->id;
        } else {
            $namespace_id = Utilities::getCurrentNamespace()?->id;
        }
        $build = LocationBound::buildLocationBound(
            namespace_id: $namespace_id,
            with_namespace: true
        )->orderBy('created_at');
        $cursor = $build->cursorPaginate(perPage: config('hbc.pagination.default_page_size'), cursor: $params->cursor);
        return Location::collect($cursor, CursorPaginatedDataCollection::class);
    }

}

