<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\Models\LocationBound;
use App\OpenApi\Params\Listing\Design\ListLocationParams;
use App\OpenApi\Results\Bounds\LocationCollectionResponse;

use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as CodeOf;

#[ApiParamMarker( param_class: ListLocationParams::class)]
class ListLocations extends Api\DesignApi
{
    const UUID = 'db5971de-fe4e-498e-b2a5-12990cdb2b26';
    const TYPE_NAME = 'api_design_list_locations';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
    ];


    public function __construct(
        protected ?ListLocationParams $params = null,

        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init,
            is_async: $this->is_async,tags: $this->tags);
    }

    protected function restoreParams(array $param_array) {
        parent::restoreParams($param_array);
        if(!$this->params) {
            $this->params = new ListLocationParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    const PRIMARY_SNAPSHOT_KEY = 'locations';
    const int HTTP_CODE_GOOD = CodeOf::HTTP_OK;

    protected function getMyData() :array {
        $build = LocationBound::buildLocationBound(
            namespace_id: $this->params->getGivenNamespace()?->id
        );

        return [static::PRIMARY_SNAPSHOT_KEY=>$build->cursorPaginate(cursor: $this->params->getCursor())];
    }


    public function getDataSnapshot(): array|IThingBaseResponse
    {
        $what =  $this->getMyData();
        return new LocationCollectionResponse(given_attributes:  $what[static::PRIMARY_SNAPSHOT_KEY],thing: $this->getMyThing());
    }

}

