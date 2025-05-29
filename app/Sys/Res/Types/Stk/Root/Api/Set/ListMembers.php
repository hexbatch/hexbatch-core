<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\Models\ElementSet;
use App\OpenApi\Params\Listing\Elements\ListElementParams;

use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Api\Element\ListElements;


#[ApiParamMarker( param_class: ListELementParams::class)]
class ListMembers extends ListElements
{
    const UUID = 'cd570e6a-8a1f-4d96-9cfa-76708d501346';
    const TYPE_NAME = 'api_set_list_members';

    const PARENT_CLASSES = [
        Api\SetApi::class
    ];

    public function __construct(
        protected ?ListELementParams $params = null,
        protected ?ElementSet $given_set = null,
        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected array          $tags = []
    )
    {
        if ($this->given_set) {
            $this->params?->setGivenSet($this->given_set);
        }
        parent::__construct(params: $this->params,action_data: $this->action_data,
            b_type_init: $this->b_type_init,is_async: $this->is_async,tags: $this->tags);
    }

}

