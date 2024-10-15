<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use App\Models\UserNamespaceMember;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses UserNamespace::owner_user()
 * @uses UserNamespace::namespace_members()
 * @uses UserNamespace::namespace_admins()
 * @uses UserNamespace::isUserAdmin()
 * @uses UserNamespace::isDefault()
 * @uses UserNamespace::$ref_uuid
 * @uses UserNamespace::$namespace_user_id
 *
 * @method  owner_user()
 * @method  isDefault()
 * @method UserNamespaceMember[]|Collection  namespace_members()
 * @method UserNamespaceMember[]|Collection namespace_admins()
 * @method isUserAdmin(\App\Models\User $user)
 * @method getName()
 *
 * @property string ref_uuid
 * @property string namespace_user_id
 */
class UserNamespaceResource extends JsonResource
{
    protected int $n_display_level = 1;
    public function __construct($resource, mixed $unused = null,int $n_display_level = 1) {
        parent::__construct($resource);
        Utilities::ignoreVar($unused);
        $this->n_display_level = $n_display_level;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (!$this->id) {return [];}

        if ($this->n_display_level <=0) {
            return ['namespace_name' => [$this->getName()] ];
        }

        return [
            'namespace_name' => $this->getName(),
            'is_default' => $this->isDefault(),
            'uuid' => $this->ref_uuid,
            'members_count' => $this->namespace_members()->count(),
            'admins_count' => $this->namespace_admins()->count(),
            'is_admin' => $this->isUserAdmin(Utilities::getTypeCastedAuthUser()),
            'is_owner' => Utilities::getTypeCastedAuthUser()?->id === $this->namespace_user_id,

        ];
    }
}
