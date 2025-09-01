<?php

namespace App\OpenApi\Params\Users;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\OpenApi\ApiDataTrait;
use App\OpenApi\Common\Resources\HexbatchResourceName;
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

class LoginParams
{
    use ApiDataTrait;

    #[OA\Property(ref: '#/components/schemas/HexbatchResourceName', title: 'User Name',
        example: [new OA\Examples(summary: "user name example", value:'will_fart') ]

    )]
    protected string $username;

    #[OA\Property(  title: 'Password',type: 'string',
                    example: [new OA\Examples(summary: "password set up in the registration", value:'beans_r_88good') ]
    )]
    protected string $password;

    public function fromCollection(\Illuminate\Support\Collection $collection)
    {
        $this->username =  static::stringFromCollection($collection,'username');
        $this->password =  static::stringFromCollection($collection,'password');
        $this->validate();
    }

    protected function validate() {
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
