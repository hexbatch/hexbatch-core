<?php

namespace App\Api\Users\Registration;

use App\Actions\Fortify\CreateNewUser;
use App\Api\BaseParams;
use App\Api\IApiOaParams;
use App\Api\Users\HexbatchUserName;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\User;
use App\Rules\UserNameReq;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

/**
 * Login requires a username and password
 */
#[OA\Schema(schema: 'RegistrationParams',
            example: [new OA\Examples(summary: "Registration Example", value: ["username"=>"will","password"=>"xamp"])]
)]

class RegistrationParams implements IApiOaParams
{
    use BaseParams;

    #[OA\Property(title: 'User Name',type: HexbatchUserName::class,
        example: [new OA\Examples(summary: "user name example", value:'will_fart') ]

    )]
    protected string $username;

    #[OA\Property(  title: 'Password',type: 'password',
                    example: [new OA\Examples(summary: "password set up in the registration", value:'beans_r_88good') ]
    )]
    protected string $password;

    public function fromCollection(\Illuminate\Support\Collection $collection)
    {

        // todo add the user to the thing data, make this a thinger and for the user register,
        //  have the user creation action a child of the create default namespace action (some new actions)

        //todo if the api is get only and does not processing at all (list from a select statement or me) it still goes through the thing but is not deferred
        $this->username =  static::stringFromCollection($collection,'username');
        $this->password =  static::stringFromCollection($collection,'password');
        $this->validate();
    }

    protected function validate() {
        try {
            Validator::make(
                ['username' => $this->username,'password'=>$this->password],
                [
                    'username'=>['required','string','min:3',Rule::unique(User::class,'username'),new UserNameReq],
                    'password' => CreateNewUser::getPasswordRules(),
                ]
            )->validate();

        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BAD_REGISTRATION);
        }
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }


}
