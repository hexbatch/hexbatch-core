<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;


use App\OpenApi\Params\Listing\Design\ShowDesignParams;

use App\OpenApi\Results\Types\TypeResponse;
use App\Sys\Res\Types\Stk\Root\Api;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as CodeOf;


#[ApiParamMarker( param_class: ShowDesignParams::class)]
class ShowDesign extends Api\DesignApi
{
    const UUID = 'd3cbd497-e670-4cd9-9f80-88505d973747';
    const TYPE_NAME = 'api_design_show';


    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];

    public function __construct(
        protected ?ShowDesignParams $params = null,

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
            $this->params = new ShowDesignParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    const PRIMARY_SNAPSHOT_KEY = 'type';
    const int HTTP_CODE_GOOD = CodeOf::HTTP_OK;

    protected function getMyData() :array {
        return [static::PRIMARY_SNAPSHOT_KEY=>$this->params->getGivenType()];
    }


    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what[static::PRIMARY_SNAPSHOT_KEY])) {
            $ret[static::PRIMARY_SNAPSHOT_KEY] = new TypeResponse(
                given_type:  $this->params->getGivenType(),
                namespace_levels:  $this->params->getNamespaceLevels(),
                parent_levels:  $this->params->getParentLevels(),
                attribute_levels:  $this->params->getAttributeLevels(),
                inherited_attribute_levels:  $this->params->getInheritedAttributeLevels(),
                number_time_spans:  $this->params->getNumberTimeSpans(),
            );
        }
        return $ret;
    }

}

