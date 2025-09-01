<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Annotations\ApiParamMarker;
use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\ElementSet;
use App\OpenApi\Params\Listing\Set\ListSetParams;
use App\OpenApi\Results\Set\SetCollectionResponse;
use App\Sys\Res\Types\Stk\Root\Api;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as CodeOf;

#[ApiParamMarker( param_class: ListSetParams::class)]
class ListSets extends Api\SetApi
{
    const UUID = 'bd6a0fef-b3bf-4f33-8988-6714ff385d71';
    const TYPE_NAME = 'api_set_sets';


    const PARENT_CLASSES = [
        Api\SetApi::class
    ];

    public function __construct(
        protected ?ListSetParams $params = null,

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
            $this->params = new ListSetParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    const PRIMARY_SNAPSHOT_KEY = 'sets';
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
        $build = ElementSet::buildSet(
            parent_set_id: $this->params->getGivenParentSet()?->id,
            type_id: $this->params->getGivenType()?->id,
            phase_id: $this->params->getWorkingPhase()?->id,
            namespace_id: $this->params->getGivenNamespace()?->id,
            in_namespace_ids: $belongs_to_namespaces,
            b_do_relations: true
        );

        return [static::PRIMARY_SNAPSHOT_KEY=>$build->cursorPaginate(cursor: $this->params->getCursor())];
    }


    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret[static::PRIMARY_SNAPSHOT_KEY] = new SetCollectionResponse(
            given_sets:  $what[static::PRIMARY_SNAPSHOT_KEY],
            show_definer:  $this->params->isShowDefiner(),
            show_parent :  $this->params->isShowParent(),
            show_elements :  $this->params->isShowElements(),
            definer_type_level:  $this->params->getDefinerTypeLevel(),
            children_set_level:  $this->params->getChildrenSetLevel(),
            parent_set_level:  $this->params->getParentSetLevel(),
        );
        return $ret;
    }

}

