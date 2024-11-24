<?php

namespace App\Api\Calls\User;

use App\Api\Common\HexbatchUuid;
use App\Api\IApiOaResponse;
use App\Models\User;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

/**
 * Show details about the logged-in user
 */
#[OA\Schema(schema: 'Me')]
class MeResponse implements IApiOaResponse
{
    #[OA\Property(title: 'User unique id',type: HexbatchUuid::class)]
    public string $uuid;

    #[OA\Property(title: 'User name')]
    public string $username;

    #[OA\Property(title: 'When the user was registered',format: 'date-time')]
    public string $registered_at;


    public function __construct(User $user)
    {
        $this->uuid = $user->ref_uuid;
        $this->username = $user->username;
        $this->registered_at = Carbon::createFromTimestamp($user->created_at_ts,config('app.timezone'))->toIso8601String();
    }


}
