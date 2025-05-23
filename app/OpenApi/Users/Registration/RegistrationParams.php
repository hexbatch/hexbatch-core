<?php

namespace App\OpenApi\Users\Registration;

use App\Actions\Fortify\CreateNewUser;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\User;
use App\OpenApi\Users\HexbatchUserName;
use App\Rules\UserNameReq;
use Illuminate\Http\Request;
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

class RegistrationParams
{
    #[OA\Property(title: 'User Name',type: HexbatchUserName::class,
        example: [new OA\Examples(summary: "user name example", value:'will_fart') ]

    )]
    protected string $username;

    #[OA\Property(  title: 'Password',type: 'password',minLength: 10,
        example: [new OA\Examples(summary: "password set up in the registration", value:'beans_r_88good') ]
    )]
    protected string $password;


    #[OA\Property(  title: 'Public key',type: 'string',minLength: 10,
        example: [new OA\Examples(summary: "optional public key to show data later is valid", value:'any public key') ]
    )]
    protected string $public_key;

    public function fromRequest(Request $request)
    {
        $this->username = $request->request->getString('username');
        $this->password = $request->request->getString('password');
        $this->public_key = (string)$request->request->get('public_key');
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

    public function getPublicKey(): string
    {
        return $this->public_key;
    }


}
