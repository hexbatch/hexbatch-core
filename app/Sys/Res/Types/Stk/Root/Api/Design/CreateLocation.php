<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\ApiParamMarker;
use App\Models\ActionDatum;
use App\OpenApi\Params\Actioning\Design\DesignLocationParams;
use App\OpenApi\Results\Bounds\LocationResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Illuminate\Support\Collection;

#[ApiParamMarker( param_class: DesignLocationParams::class)]
class CreateLocation extends Api\DesignApi
{
    const UUID = '508437a6-6307-4dba-b9f0-8ff14c91f583';
    const TYPE_NAME = 'api_design_location';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignLocationCreate::class,
    ];

    public function __construct(
        protected ?DesignLocationParams $params = null,

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
            $this->params = new DesignLocationParams();
            $this->params->fromCollection(new Collection($param_array),false);
        }
    }

    protected function getMyData() :array {
        return ['bound'=>$this->getGivenLocationBound()];
    }

    public function getDataSnapshot(): array|IThingBaseResponse
    {
        $what =  $this->getMyData();
        return new LocationResponse(given_location:  $what['bound'],thing: $this->getMyThing());
    }






    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignLocationCreate(
            bound_name: $this->params->getBoundName(),
            location_type: $this->params->getLocationType(),
            geo_json: $this->params->getGeoJson(),
            display: $this->params->getDisplay(),
            parent_action_data: $this->action_data,tags: ['create location bound from api']);
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1, 'title' => $creator->getType()->getName(),'action'=>$creator];


        //last in tree is the
        if (count($nodes)) {
            return new Tree(
                $nodes,
                ['rootId' => -1]
            );
        }
        return null;

    }


    /**
     * @throws \Exception
     */
    public function setChildActionResult(IThingAction $child): void {

        if ($child instanceof Act\Cmd\Ds\DesignLocationCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenLocationBound($child->getGivenLocationBound());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }

}

