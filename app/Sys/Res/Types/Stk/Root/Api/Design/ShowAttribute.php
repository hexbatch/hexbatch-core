<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\ApiResults\Attribute\ApiAttributeResponse;
use App\OpenApi\Params\Listing\Design\ShowAttributeParams;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as CodeOf;


#[ApiParamMarker( param_class: ShowAttributeParams::class)]
class ShowAttribute extends Api\DesignApi
{
    const UUID = '681e3f6e-9410-4356-a157-4d99580c0232';
    const TYPE_NAME = 'api_design_show_attribute';

    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];


    public function __construct(
        protected ?ShowAttributeParams $params = null,

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
            $this->params = new ShowAttributeParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    const PRIMARY_SNAPSHOT_KEY = 'attribute';
    const int HTTP_CODE_GOOD = CodeOf::HTTP_OK;

    protected function getMyData() :array {
        return [static::PRIMARY_SNAPSHOT_KEY=>$this->params->getGivenAttribute()];
    }


    public function getDataSnapshot(): array |IThingBaseResponse
    {
        return new ApiAttributeResponse(
            given_attribute:  $this->params->getGivenAttribute(),
            attribute_levels:  $this->params->getAttributeLevels(),
            owning_type_levels :  $this->params->getOwningTypeLevels(),
            design_levels:  $this->params->getDesignLevels(),thing: $this->getMyThing()
        );
    }

}

