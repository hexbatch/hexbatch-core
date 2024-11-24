<?php
namespace App\Api\Cmd\Users\Create;



use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act;

class RegisterForSystem extends UserCreateParams
{


    public function doParamsAndResponse()  :NewUserReturn {
        /**
         * @var UserCreateParams $reg_params
         */
        $reg_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,Act\Cmd\Us\UserRegister::getClassUuid());
        $reg_params->fromCollection($this->makeCollection());

        /**
         * @type UserCreateResponse $promo_work
         */
        $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,Act\Cmd\Us\UserRegister::getClassUuid());

        /**
         * @type NewUserReturn
         */
        return  $promo_work::doWork($reg_params);
    }


}
