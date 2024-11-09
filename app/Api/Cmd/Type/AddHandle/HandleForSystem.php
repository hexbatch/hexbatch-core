<?php
namespace App\Api\Cmd\Type\AddHandle;

use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\TypeHandleAdd;

class HandleForSystem
{
    use SharedHandleParams;


    /** @param  int[] $type_ids */
    public function setTypeIds(array $type_ids): HandleForSystem
    {
        $this->type_ids = $type_ids;
        return $this;
    }

    public function setHandleElementId(?int $handle_element_id): HandleForSystem
    {
        $this->handle_element_id = $handle_element_id;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function doParamsAndResponse() :array {
        /**
         * @var AddHandleParams $promo_params
         */
        $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,TypeHandleAdd::getClassUuid());
        $promo_params->fromCollection($this->makeCollection());

        /**
         * @type AddHandleResponse $promo_work
         */
        $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,TypeHandleAdd::getClassUuid());

        /** @var AddHandleResponse $promo_results */
        $promo_results = $promo_work::doWork($promo_params);
        return $promo_results->getEditedTypes();
    }


}
