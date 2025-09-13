<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\Models\ElementType;
use App\OpenApi\Params\Listing\Elements\ListElementParams;

use App\Sys\Res\Types\Stk\Root\Api;


#[ApiParamMarker( param_class: ListELementParams::class)]
class ListElementsOfType extends Api\Element\ListElements
{
    const UUID = '70e9fd26-ab9c-4259-a4d4-32ff4803868f';
    const TYPE_NAME = 'api_type_list_elements';


    const PARENT_CLASSES = [
        Api\TypeApi::class
    ];

    public function __construct(
        protected ?ListELementParams $params = null,
        protected ?ElementType $given_type = null,
        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected array          $tags = []
    )
    {
        if ($this->given_type) {
            $this->params?->setGivenType($this->given_type);
        }
        parent::__construct(params: $this->params,action_data: $this->action_data,
            b_type_init: $this->b_type_init,is_async: $this->is_async,tags: $this->tags);
    }

}

