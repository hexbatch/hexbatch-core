<?php

namespace App\OpenApi\UserNamespaces;

use App\Api\Common\HexbatchUuid;
use App\Models\UserNamespace;
use Carbon\Carbon;
use JsonSerializable;
use OpenApi\Attributes as OA;


/**
 * Show details about the logged-in user
 */
#[OA\Schema(schema: 'UserNamespaceResponse')]
class UserNamespaceResponse implements  JsonSerializable
{
    #[OA\Property(title: 'User namespace uuid',type: HexbatchUuid::class)]
    public string $uuid = '';

    #[OA\Property(title: 'Home set uuid',type: HexbatchUuid::class)]
    public string $home_set_uuid = '';

    #[OA\Property(title: 'Public element uuid',type: HexbatchUuid::class)]
    public string $public_element_uuid = '';

    #[OA\Property(title: 'Private element uuid',type: HexbatchUuid::class)]
    public string $private_element_uuid = '';

    #[OA\Property(title: 'Base type uuid',type: HexbatchUuid::class)]
    public string $base_type_uuid = '';


    #[OA\Property(title: 'Namespace created at',format: 'date-time')]
    public ?string $namespace_created_at = '';





    public function __construct(protected ?UserNamespace $namespace = null)
    {
        if ($namespace) {
            $this->uuid = $namespace->ref_uuid;
            $this->base_type_uuid = $namespace->namespace_base_type->ref_uuid;
            $this->private_element_uuid = $namespace->private_element->ref_uuid;
            $this->public_element_uuid = $namespace->public_element->ref_uuid;
            $this->home_set_uuid = $namespace->home_set->ref_uuid;
            $this->namespace_created_at = $namespace->created_at? Carbon::parse($namespace->created_at,config('app.timezone'))->toIso8601String():null;
        }

    }


    public function jsonSerialize(): array
    {
        $ret = [];
        $ret['uuid'] = $this->uuid;
        $ret['base_type_uuid'] = $this->base_type_uuid;
        $ret['private_element_uuid'] = $this->private_element_uuid;
        $ret['public_element_uuid'] = $this->public_element_uuid;
        $ret['home_set_uuid'] = $this->home_set_uuid;
        $ret['namespace_created_at'] = $this->namespace_created_at;
        return $ret;
    }

}
