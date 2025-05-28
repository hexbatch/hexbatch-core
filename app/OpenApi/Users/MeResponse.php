<?php

namespace App\OpenApi\Users;

use App\Api\Common\HexbatchUuid;
use App\Models\User;
use Carbon\Carbon;
use Hexbatch\Things\Interfaces\ICallResponse;
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
    public ?string $registered_at = '';


    #[OA\Property(title: 'Namespace uuid',type: HexbatchUuid::class)]
    public string $namespace_uuid = '';



    public function __construct(protected ?User $user = null)
    {
        if ($user) {
            $this->uuid = $user->ref_uuid;
            $this->username = $user->username;
            $this->registered_at = $user->created_at? Carbon::parse($user->created_at,config('app.timezone'))->toIso8601String():null;
            $this->namespace_uuid = $user->default_namespace?->ref_uuid;
        }

    }


    public function jsonSerialize(): array
    {
        $ret = [];
        $ret['uuid'] = $this->uuid;
        $ret['username'] = $this->username;
        $ret['registered_at'] = $this->registered_at;
        $ret['namespace_uuid'] = $this->namespace_uuid;
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


    public function getWaitTimeoutInSeconds(): ?int
    {
        return null;
    }
}
