<?php
namespace App\Api\Cmd\Type\AttributeAddHandle;

use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\AttributeHandleAdd;

class AttributeHandleForSystem
{
    use SharedAttributeHandleParams;

    /** @param  int[] $attribute_ids */
    public function setAttributeIds(array $attribute_ids): AttributeHandleForSystem
    {
        $this->attribute_ids = $attribute_ids;
        return $this;
    }

    public function setHandleAttributeId(?int $handle): AttributeHandleForSystem
    {
        $this->handle_attribute_id = $handle;
        return $this;
    }




    /**
     * @throws \Exception
     */
    public function doParamsAndResponse() :array {
        /**
         * @var AttributeAddHandleParams $promo_params
         */
        $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,AttributeHandleAdd::getClassUuid());
        $promo_params->fromCollection($this->makeCollection());

        /**
         * @type AttributeAddHandleResponse $promo_work
         */
        $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,AttributeHandleAdd::getClassUuid());

        /** @var AttributeAddHandleResponse $promo_results */
        $promo_results = $promo_work::doWork($promo_params);
        return $promo_results->getEditedAttributes();
    }


}
