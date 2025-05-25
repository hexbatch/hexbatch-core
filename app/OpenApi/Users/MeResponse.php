<?php

namespace App\OpenApi\Users;

use App\Api\Common\HexbatchUuid;
use App\Models\User;
use Carbon\Carbon;
use Hexbatch\Things\Interfaces\ICallResponse;
use Hexbatch\Things\Models\ThingCallback;
use JsonSerializable;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as CodeOf;

/**
 * Show details about the logged-in user
 */
#[OA\Schema(schema: 'Me')]
class MeResponse implements  JsonSerializable,ICallResponse
{
    #[OA\Property(title: 'User unique id',type: HexbatchUuid::class)]
    public string $uuid = '';

    #[OA\Property(title: 'User name')]
    public string $username = '';

    #[OA\Property(title: 'When the user was registered',format: 'date-time')]
    public string $registered_at = '';



    public function __construct(protected ?User $user = null)
    {
        if ($user) {
            $this->uuid = $user->ref_uuid;
            $this->username = $user->username;
            $this->registered_at = Carbon::createFromTimestamp($user->created_at_ts,config('app.timezone'))->toIso8601String();
        }

    }


    public function jsonSerialize(): array
    {
        $ret = [];
        $ret['uuid'] = $this->uuid;
        $ret['username'] = $this->username;
        $ret['registered_at'] = $this->registered_at;
        return $ret;
    }

    public function getCode(): int
    {
        if ($this->user && $this->user->default_namespace_id) {
            return CodeOf::HTTP_OK;
        }
        return CodeOf::HTTP_UNPROCESSABLE_ENTITY; //it was blocked somewhere
    }

    public function getData(): ?array
    {
        return $this->jsonSerialize();
    }

    public static function fromCallback(ThingCallback $callback) : ?MeResponse {
        return null;
        //todo fill in me from callback, and also put in a callback reference here
    }
}
