<?php
namespace App\Api\Cmd\Namespace\EditPromotion;



use App\Api\Cmd\Namespace\Promote\NsForSystem;
use App\Exceptions\HexbatchInitException;
use App\Models\UserNamespace;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\NamespaceEditPromotion;


class NsEditForSystem extends NsForSystem
{

    public function doParamsAndResponse() :UserNamespace {
        try
        {

            /**
             * @var NamespaceEditPromotionParams $promo_params
             */
            $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,NamespaceEditPromotion::getClassUuid());
            $promo_params->fromCollection($this->makeCollection());

            /**
             * @type NamespaceEditPromotionResponse $promo_work
             */
            $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,NamespaceEditPromotion::getClassUuid());

            /**
             * @type NamespaceEditPromotionResponse $promo_results
             */
            $promo_results = $promo_work::doWork($promo_params);
            return $promo_results->getEditedNamespace();

        } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
        }
    }



}
