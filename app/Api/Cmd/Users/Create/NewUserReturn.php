<?php

namespace App\Api\Cmd\Users\Create;

use App\Api\Cmd\IActionOaResponse;

use App\Api\Cmd\IActionWorkReturn;
use App\Api\Common\HexbatchUuid;

use App\Models\User;
use Carbon\Carbon;
use OpenApi\Attributes as OA;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * Show details about the logged-in user
 */
#[OA\Schema(schema: 'NewUserResponse')]
class NewUserReturn extends Act\Cmd\Us\UserRegister implements IActionOaResponse,IActionWorkReturn
{
    #[OA\Property(title: 'New user uuid',type: HexbatchUuid::class)]
    public string $uuid;

    #[OA\Property(title: 'New user name')]
    public string $username;

    #[OA\Property(title: 'When the user was registered',format: 'date-time')]
    public string $registered_at;


    public function __construct(
        protected ?User $user = null
    )
    {

        $this->uuid = $user->ref_uuid;
        $this->username = $user->username;
        $this->registered_at = Carbon::createFromTimestamp($user->created_at_ts,config('app.timezone'))->toIso8601String();
        parent::__construct();
    }


    public function toThing( $thing)
    {
        $thing->setCurrentData([$this->user]);
    }
}
