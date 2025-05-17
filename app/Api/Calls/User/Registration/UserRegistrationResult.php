<?php
namespace App\Api\Calls\User\Registration;


use App\Api\Cmd\Users\Create\NewUserReturn;

use App\Api\IApiOaResponse;
use App\Models\User;
use App\Sys\Res\Types\Stk\Root\Api;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'UserRegistrationResult' )]
class UserRegistrationResult  extends Api\User\UserRegister implements IApiOaResponse
{

    #[OA\Property(title: 'Created User')]
    public NewUserReturn $created_user;


    public function __construct(User $user)
    {
        $this->created_user = new NewUserReturn(user: $user);
    }

}
