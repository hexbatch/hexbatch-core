<?php

namespace App\Api\Users\Login;

use App\Api\IApiOaParams;
use App\Api\Users\HexbatchUserName;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

/**
 * Login requires a username and password
 */
#[OA\Schema(schema: 'LoginParams',example: [
    new OA\Examples(summary: "logging in example", value: ["username"=>"will","password"=>"xamp"]) ,
    new OA\Examples(summary: "logging 2 in example", value: ["username"=>"will2","password"=>"xamp22"]) ,
])]

class LoginParams implements IApiOaParams
{
    #[OA\Property(title: 'User Name',type: HexbatchUserName::class,
        example: [new OA\Examples(summary: "user name example", value:'will_fart') ]

    )]
    protected string $username;

    #[OA\Property(  title: 'Password',type: 'password',
                    example: [new OA\Examples(summary: "password set up in the registration", value:'beans_r_88good') ]
    )]
    protected string $password;

    public function fromRequest(Request $request)
    {
        $this->username = $request->request->getString('username');
        $this->password = $request->request->getString('password');
        try {
            Validator::make(
                ['username' => $this->username,'password'=>$this->password],
                [
                    'username' => ['required', 'string', 'max:300'],
                    'password' => ['required', 'string', 'max:300']
                ])->validate();

        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BAD_LOGIN);
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
