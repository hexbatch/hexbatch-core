<?php
namespace App\Api\Calls\User\Registration;


use App\Api\Calls\Design\Promote\DesignPromoteResponse;
use App\Api\Calls\IApiThingResult;
use App\Api\Cmd\Users\Create\NewUserReturn;
use App\Api\IApiOaResponse;
use App\Models\ThingResult;
use App\Sys\Res\Types\Stk\Root\Api;
use OpenApi\Attributes as OA;
/**
 * This is only called by the thing if there is no errors
 */
class UserRegistrationResponse  extends Api\User\UserRegister implements IApiThingResult,IApiOaResponse
{

    #[OA\Property(title: 'Created User')]
    protected NewUserReturn $created_user;
    /**
     * @param DesignPromoteResponse $api_result
     */
    public static function writeReturn(ThingResult $result, $api_result): void
    {
        //todo : here
    }

}
