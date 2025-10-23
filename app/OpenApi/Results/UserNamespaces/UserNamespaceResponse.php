<?php

namespace App\OpenApi\Results\UserNamespaces;

use App\Models\UserNamespace;
use App\OpenApi\Common\HexbatchUuid;
use App\OpenApi\Results\ResultBase;
use App\OpenApi\Results\Set\SetResponse;
use Carbon\Carbon;
use OpenApi\Attributes as OA;


/**
 * Show details about a namespace
 */
#[OA\Schema(schema: 'UserNamespaceResponse')]
class UserNamespaceResponse extends ResultBase
{
    #[OA\Property(title: 'User namespace uuid',type: HexbatchUuid::class)]
    public string $uuid = '';

    #[OA\Property(title: 'Home set uuid',type: HexbatchUuid::class)]
    public string $home_set_uuid = '';

    #[OA\Property(title: 'Namespace name')]
    public string $namespace_name = '';

    #[OA\Property(title: 'Public element uuid',type: HexbatchUuid::class)]
    public string $public_element_uuid = '';

    #[OA\Property(title: 'Private element uuid',type: HexbatchUuid::class)]
    public string $private_element_uuid = '';

    #[OA\Property(title: 'Base type uuid',type: HexbatchUuid::class)]
    public string $base_type_uuid = '';


    #[OA\Property(title: 'Namespace created at',format: 'date-time')]
    public ?string $namespace_created_at = '';


    #[OA\Property(title: 'Namespace created at')]
    public ?SetResponse $home_set = null;





    public function __construct(protected ?UserNamespace $namespace = null, bool $show_homeset = false)
    {
        parent::__construct();
        if ($namespace) {
            $this->namespace_name = $this->namespace->namespace_name;
            $this->uuid = $namespace->ref_uuid;
            $this->base_type_uuid = $namespace->namespace_base_type->ref_uuid;
            $this->private_element_uuid = $namespace->private_element->ref_uuid;
            $this->public_element_uuid = $namespace->public_element->ref_uuid;
            $this->home_set_uuid = $namespace->home_set->ref_uuid;
            $this->namespace_created_at = $namespace->created_at? Carbon::parse($namespace->created_at,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;
        }

        if ($show_homeset) {
            $this->home_set = new SetResponse(given_set: $this->namespace->home_set,
                show_definer: true, show_elements: true, definer_type_level: 1);
        }

    }


    public  function toArray() : array  {
      $ret = parent::toArray();
        unset($ret['namespace']);
        $ret['uuid'] = $this->uuid;
        $ret['namespace_name'] = $this->namespace_name;
        $ret['base_type_uuid'] = $this->base_type_uuid;
        $ret['private_element_uuid'] = $this->private_element_uuid;
        $ret['public_element_uuid'] = $this->public_element_uuid;
        $ret['home_set_uuid'] = $this->home_set_uuid;
        $ret['namespace_created_at'] = $this->namespace_created_at;

        if ($this->home_set) {
            $ret['home_set'] = $this->home_set;
        }
        return $ret;
    }

}
