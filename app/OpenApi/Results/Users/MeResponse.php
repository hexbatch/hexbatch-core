<?php

namespace App\OpenApi\Results\Users;

use App\Models\User;
use App\OpenApi\Common\HexbatchUuid;
use App\OpenApi\Results\ResultBase;
use App\OpenApi\Results\UserNamespaces\UserNamespaceResponse;
use Carbon\Carbon;
use Hexbatch\Things\Interfaces\ICallResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as CodeOf;

/**
 * Show details about the logged-in user
 */
#[OA\Schema(schema: 'Me')]
class MeResponse extends ResultBase implements ICallResponse
{
    #[OA\Property(title: 'User unique id',type: HexbatchUuid::class)]
    public string $uuid = '';

    #[OA\Property(title: 'User name')]
    public string $username = '';

    #[OA\Property(title: 'When the user was registered',format: 'date-time')]
    public ?string $registered_at = '';


    #[OA\Property(title: 'Namespace uuid',type: HexbatchUuid::class)]
    public ?string $namespace_uuid = '';

    #[OA\Property(title: 'Namespace',type: HexbatchUuid::class)]
    public ?UserNamespaceResponse $namespace = null;



    public function __construct(protected ?User $user = null, bool $show_namespace = false)
    {
        parent::__construct();
        if ($user) {
            $this->uuid = $user->ref_uuid;
            $this->username = $user->username;
            $this->registered_at = $user->created_at? Carbon::parse($user->created_at,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;
            $this->namespace_uuid = $user->default_namespace?->ref_uuid;
        }

        if ($this->user?->default_namespace && $show_namespace) {
            $this->namespace = new UserNamespaceResponse(namespace: $this->user->default_namespace,show_homeset: true);
        }

    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        unset($ret['user']);
        unset($ret['show_namespace']);
        $ret['uuid'] = $this->uuid;
        $ret['username'] = $this->username;
        $ret['registered_at'] = $this->registered_at;
        $ret['namespace_uuid'] = $this->namespace_uuid;

        if ($this->namespace) {
            $ret['namespace'] = $this->namespace;
        }
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
