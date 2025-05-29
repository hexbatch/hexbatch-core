<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\ApiResults\Set\ApiSetResponse;
use App\OpenApi\Params\Listing\Set\ShowSetParams;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as CodeOf;


#[ApiParamMarker( param_class: ShowSetParams::class)]
class ShowSet extends Api\SetApi
{
    const UUID = 'b71a08ad-ca4f-40fa-9aac-9973a45cb44d';
    const TYPE_NAME = 'api_set_show';

    const PARENT_CLASSES = [
        Api\SetApi::class
    ];


    public function __construct(
        protected ?ShowSetParams $params = null,

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
            $this->params = new ShowSetParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    const PRIMARY_SNAPSHOT_KEY = 'set';
    const int HTTP_CODE_GOOD = CodeOf::HTTP_OK;


    protected function getMyData() :array {
        return [static::PRIMARY_SNAPSHOT_KEY=>$this->params->getGivenSet()];
    }


    public function getDataSnapshot(): array|IThingBaseResponse
    {
        return new ApiSetResponse(
            given_set:  $this->params->getGivenSet(),
            show_definer:  $this->params->isShowDefiner(),
            show_parent :  $this->params->isShowParent(),
            show_elements :  $this->params->isShowElements(),
            definer_type_level:  $this->params->getDefinerTypeLevel(),
            children_set_level:  $this->params->getChildrenSetLevel(),
            parent_set_level:  $this->params->getParentSetLevel(),thing: $this->getMyThing()
        );
    }

}

