<?php
namespace App\Api\Cmd\Type\PublishPromote;


use App\Enums\Types\TypeOfLifecycle;
use App\Models\ElementType;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\TypePublishPromote;

class PublishForSystem
{
    use PublishingParams;


    public function setTypeId(?int $type_id): PublishForSystem
    {
        $this->type_id = $type_id;
        return $this;
    }

    public function setParentIds(array $parent_ids): PublishForSystem
    {
        $this->parent_ids = $parent_ids;
        return $this;
    }

    public function setLifecycle(?TypeOfLifecycle $lifecycle): PublishForSystem
    {
        $this->lifecycle = $lifecycle;
        return $this;
    }


    /**
     * @return ElementType
     * @throws \Exception
     */
    public function doParamsAndResponse()  :ElementType {
        /**
         * @var PublishPromoteParams $promo_params
         */
        $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,TypePublishPromote::getClassUuid());
        $promo_params->fromCollection($this->makeCollection());

        /**
         * @type PublishPromoteResponse $promo_work
         */
        $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,TypePublishPromote::getClassUuid());

        $promo_results = $promo_work::doWork($promo_params);
        return $promo_results->getGeneratedType();
    }

}
