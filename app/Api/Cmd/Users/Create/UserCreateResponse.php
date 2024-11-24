<?php
namespace App\Api\Cmd\Users\Create;


use App\Actions\Fortify\CreateNewUser;
use App\Api\Cmd\Design\Promote\DesignPromoteResponse;

use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;

use App\Exceptions\HexbatchInvalidException;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;


use App\Sys\Res\Types\Stk\Root\Act;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class UserCreateResponse extends Act\Cmd\Us\UserRegister implements IActionWorker
{


    public function __construct(
        protected ?NewUserReturn $ret_user = null
    )
    {
        parent::__construct();
    }

    protected function run(UserCreateParams $params) {
        try {
            DB::beginTransaction();

            $user = (new CreateNewUser)->create([
                'username' => $params->getUsername(),'password'=>$params->getPassword(),
                'password_confirmation'=>$params->getPassword()]);

            $user->refresh();
            $this->ret_user = new NewUserReturn(user: $user);

            DB::commit();
        } catch (ValidationException $v) {
            DB::rollBack();
            throw new HexbatchNotPossibleException($v->getMessage(),
                CodeOf::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BAD_REGISTRATION);
        }
    }

    /**
     * @param UserCreateParams $params
     * @return DesignPromoteResponse
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,UserCreateParams::class) || is_subclass_of($params,UserCreateParams::class))) {
            throw new HexbatchInvalidException("Params is not UserRegistartionParams");
        }
        $worker = new UserCreateResponse();
        $worker->run($params);
        return $worker->ret_user;
    }





}
