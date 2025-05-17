<?php
namespace App\Api\Calls\User\Registration;


use App\Api\Calls\IApiThingResult;


use App\Helpers\Utilities;
use App\Models\User;
use App\Sys\Res\Types\Stk\Root\Api;

use Symfony\Component\HttpFoundation\Response as CodeOf;

/**
 * This is only called by the thing if there is no errors
 */
class UserRegistrationResponse  extends Api\User\UserRegister implements IApiThingResult
{


    public  function writeReturn( $result): void
    {

        $user = User::getUser(id:$data->collection_user_id);
        $result->result_response = Utilities::wrapJsonEncode(new UserRegistrationResult(user: $user));
        $result->result_http_status = CodeOf::HTTP_CREATED;
        $result->save();
    }


    public function processChildrenData( $thing): void
    {
        // TODO: Implement processChildrenData() method.
    }
}
