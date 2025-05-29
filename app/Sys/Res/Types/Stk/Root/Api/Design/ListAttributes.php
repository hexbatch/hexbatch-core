<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;

use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\OpenApi\ApiResults\Attribute\ApiAttributeCollectionResponse;
use App\OpenApi\Params\Listing\Design\ListAttributeParams;

use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as CodeOf;


#[ApiParamMarker( param_class: ListAttributeParams::class)]
class ListAttributes extends Api\DesignApi
{
    const UUID = '293ec496-e455-4dbe-8058-c6b528370268';
    const TYPE_NAME = 'api_design_list_attributes';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
    ];

    public function __construct(
        protected ?ListAttributeParams $params = null,

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
            $this->params = new ListAttributeParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    const PRIMARY_SNAPSHOT_KEY = 'attributes';
    const int HTTP_CODE_GOOD = CodeOf::HTTP_OK;


    protected function getMyData() :array {
        $belongs_to_namespaces = [];

        if (!$this->params->getGivenNamespace()) {
            $my_namespace = Utilities::getThisUserDefaultNamespace();
            /** @uses UserNamespace::namespaces_member_of()  */
            foreach ($my_namespace->namespaces_member_of as $member_of) {
                $belongs_to_namespaces[] = $member_of->id;
            }
        }
        $build = Attribute::buildAttribute(
            namespace_id: $this->params->getGivenNamespace()?->id,
            in_namespace_ids: $belongs_to_namespaces,
            shape_id: $this->params->getGivenLocation()?->id
        );

        return [static::PRIMARY_SNAPSHOT_KEY=>$build->cursorPaginate(cursor: $this->params->getCursor())];
    }


    public function getDataSnapshot(): array|IThingBaseResponse
    {
        $what =  $this->getMyData();
        return new ApiAttributeCollectionResponse(
            given_attributes:  $what[static::PRIMARY_SNAPSHOT_KEY],
            attribute_levels:  $this->params->getAttributeLevels(),
            owning_type_levels:  $this->params->getOwningTypeLevels(),
            design_levels:  $this->params->getDesignLevels(),
            thing: $this->getMyThing()
        );
    }

}

