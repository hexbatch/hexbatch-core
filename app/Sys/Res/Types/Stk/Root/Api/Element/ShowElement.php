<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;


use App\Models\ActionDatum;
use App\OpenApi\Params\Listing\Elements\ShowElementParams;
use App\OpenApi\Results\Elements\ElementResponse;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as CodeOf;


#[ApiParamMarker( param_class: ShowElementParams::class)]
class ShowElement extends Api\ElementApi
{
    const UUID = '8d80e4c7-a938-48fe-9ca0-a6807740fdd5';
    const TYPE_NAME = 'api_element_show';

    const PARENT_CLASSES = [
        Api\ElementApi::class
    ];

    public function __construct(
        protected ?ShowElementParams $params = null,

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
            $this->params = new ShowElementParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    const PRIMARY_SNAPSHOT_KEY = 'element';
    const int HTTP_CODE_GOOD = CodeOf::HTTP_OK;


    protected function getMyData() :array {
        return [static::PRIMARY_SNAPSHOT_KEY=>$this->params->getGivenElement()];
    }


    public function getDataSnapshot(): array|IThingBaseResponse
    {
        return new ElementResponse(
            given_element:  $this->params->getGivenElement(),
            type_level:  $this->params->getTypeLevel(),
            attribute_level :  $this->params->getAttributeLevel(),
            namespace_level:  $this->params->getNamespaceLevel(),
            phase_level:  $this->params->getPhaseLevel(),thing: $this->getMyThing()
        );
    }

}

