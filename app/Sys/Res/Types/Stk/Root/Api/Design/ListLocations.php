<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Data\ApiParams\Data\Locations\Location;
use App\Data\ApiParams\Data\Locations\Params\LocationSearchParams;
use App\Data\ApiParams\Data\Schedules\Schedule;
use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\LocationBound;
use App\Models\UserNamespace;
use App\OpenApi\Params\Listing\Design\ListLocationParams;

use App\Sys\Res\Types\Stk\Root\Api;
use Spatie\LaravelData\CursorPaginatedDataCollection;

#[ApiParamMarker( param_class: ListLocationParams::class)]
class ListLocations extends Api\DesignApi
{
    const UUID = 'db5971de-fe4e-498e-b2a5-12990cdb2b26';
    const TYPE_NAME = 'api_design_list_locations';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
    ];


    public function __construct(
        protected ?LocationSearchParams $params = null,

        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init,
            is_async: $this->is_async,tags: $this->tags);
    }

    protected function getMyData() :array {
        $namespace_id = null;
        if ($this->params->namespace_ref) {
            $namespace_id = UserNamespace::resolveNamespace(value: $this->params->namespace_ref)->id;
        }
        $build = LocationBound::buildLocationBound(
            namespace_id: $namespace_id
        );

        return [static::PRIMARY_SNAPSHOT_KEY=>$build->cursorPaginate(cursor: $this->params->cursor)];
    }


    public function getDataSnapshot(): CursorPaginatedDataCollection
    {
        $what =  $this->getMyData();
        $locs = $what[static::PRIMARY_SNAPSHOT_KEY];
        $resp = Location::collect($locs, CursorPaginatedDataCollection::class);
        return $resp;
    }


    /**
     * @return CursorPaginatedDataCollection<Schedule>
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
        $cursor = $build->cursorPaginate(perPage: 15, cursor: $params->cursor);
        return Location::collect($cursor, CursorPaginatedDataCollection::class);
    }

}

