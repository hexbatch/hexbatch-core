<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Enums\Types\TypeOfLifecycle;
use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\ElementType;

use App\Models\UserNamespace;
use App\OpenApi\Params\Listing\Design\ListDesignParams;
use App\OpenApi\Results\Types\TypeCollectionResponse;
use App\Sys\Res\Types\Stk\Root\Api;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as CodeOf;

#[ApiParamMarker( param_class: ListDesignParams::class)]
class ListDesigns extends Api\DesignApi
{
    const UUID = '8b1513d3-5a01-4e6f-979e-3584bbec14af';
    const TYPE_NAME = 'api_design_list';


    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];

    public function __construct(
        protected ?ListDesignParams $params = null,

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
            $this->params = new ListDesignParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    const PRIMARY_SNAPSHOT_KEY = 'designs';
    const int HTTP_CODE_GOOD = CodeOf::HTTP_OK;

    const FILTER_OF_LIFECYCLE = TypeOfLifecycle::DEVELOPING;

    protected function getMyData() :array {
        $belongs_to_namespaces = [];

        if (!$this->params->getGivenNamespace()) {
            $my_namespace = Utilities::getThisUserDefaultNamespace();
            /** @uses UserNamespace::namespaces_member_of()  */
            foreach ($my_namespace->namespaces_member_of as $member_of) {
                $belongs_to_namespaces[] = $member_of->id;
            }
        }
        $build = ElementType::buildElementType(
            namespace_id: $this->params->getGivenNamespace()?->id,
            in_namespace_ids: $belongs_to_namespaces,
            lifecycle: static::FILTER_OF_LIFECYCLE
        );

        return [static::PRIMARY_SNAPSHOT_KEY=>$build->cursorPaginate(cursor: $this->params->getCursor())];
    }


    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret[static::PRIMARY_SNAPSHOT_KEY] = new TypeCollectionResponse(
            given_attributes:  $what[static::PRIMARY_SNAPSHOT_KEY],
            namespace_levels:  $this->params->getNamespaceLevels(),
            parent_levels:  $this->params->getParentLevels(),
            attribute_levels:  $this->params->getAttributeLevels(),
            inherited_attribute_levels:  $this->params->getInheritedAttributeLevels(),
            number_time_spans:  $this->params->getNumberTimeSpans()
        );
        return $ret;
    }

}

