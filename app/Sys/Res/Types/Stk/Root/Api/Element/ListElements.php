<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\ApiParamMarker;
use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\Element;
use App\OpenApi\Params\Listing\Elements\ListElementParams;
use App\OpenApi\Results\Elements\ElementCollectionResponse;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as CodeOf;

#[ApiParamMarker( param_class: ListELementParams::class)]
class ListElements extends Api\ElementApi
{
    const UUID = 'ec5c1437-ce47-4fcb-b8cf-88bb9dec9653';
    const TYPE_NAME = 'api_element_list';


    const PARENT_CLASSES = [
        Api\ElementApi::class
    ];


    public function __construct(
        protected ?ListELementParams $params = null,

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
            $this->params = new ListELementParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    const PRIMARY_SNAPSHOT_KEY = 'elements';
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
        $build = Element::buildElement(
            type_id: $this->params->getGivenType()?->id,
            attribute_id: $this->params->getGivenAttribute()?->id,
            shape_id:$this->params->getGivenLocation()?->id ,
            schedule_id: $this->params->getGivenSchedule()?->id,
            set_id: $this->params->getGivenSet()?->id,
            namespace_id: $this->params->getGivenNamespace()?->id,
            in_namespace_ids: $belongs_to_namespaces,
            is_set: $this->params->getIsSet(),
            b_do_relations: true
        );

        return [static::PRIMARY_SNAPSHOT_KEY=>$build->cursorPaginate(cursor: $this->params->getCursor())];
    }


    public function getDataSnapshot(): array|IThingBaseResponse
    {
        $what =  $this->getMyData();
        return  new ElementCollectionResponse(
            given_elements:  $what[static::PRIMARY_SNAPSHOT_KEY],
            type_level: $this->params->getTypeLevel(),
            attribute_level: $this->params->getAttributeLevel(),
            namespace_level: $this->params->getNamespaceLevel(),
            phase_level: $this->params->getPhaseLevel(),thing: $this->getMyThing()
        );
    }

}

