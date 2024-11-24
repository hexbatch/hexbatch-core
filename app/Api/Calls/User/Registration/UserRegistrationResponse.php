<?php
namespace App\Api\Calls\User\Registration;


use App\Api\Calls\IApiThingResult;


use App\Models\Thing;
use App\Models\ThingDatum;
use App\Models\ThingResult;
use App\Models\User;
use App\Sys\Res\Types\Stk\Root\Api;

use Symfony\Component\HttpFoundation\Response as CodeOf;

/**
 * This is only called by the thing if there is no errors
 */
class UserRegistrationResponse  extends Api\User\UserRegister implements IApiThingResult
{


    public  function writeReturn(ThingResult $result): void
    {
        /** @var ThingDatum $data */
        $data = ThingDatum::where('owning_thing_id',$result->owner_thing_id)->whereNotNull('collection_user_id')->first();
        $user = User::getUser(id:$data->collection_user_id);
        $result->result_response = \MattyRad\OpenApi\Serializer::serialize(new UserRegistrationResult(user: $user));
        $result->result_http_status = CodeOf::HTTP_CREATED;
        $result->save();
    }


    public function processChildrenData(Thing $thing): void
    {
        // TODO: Implement processChildrenData() method.
    }
}
