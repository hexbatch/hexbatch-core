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
 *
 * @method  owner_user()
 * @method UserNamespaceMember[]|Collection  namespace_members()
 * @method UserNamespaceMember[]|Collection namespace_admins()
 * @method isUserAdmin(\App\Models\User $user)
 *
 * @method group_members()
 * @method UserNamespaceMember[]|Collection group_admins()
 * @method getName()
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
            return ['group_name' => [$this->getName()] ];
        }

        return [
            'group_name' => $this->getName(),

            'uuid' => $this->ref_uuid,
            'members_count' => $this->group_members()->count(),
            'admins_count' => $this->group_admins()->count(),
            'is_admin' => $this->isUserAdmin(Utilities::getTypeCastedAuthUser()),
            'is_owner' => Utilities::getTypeCastedAuthUser()?->id === $this->namespace_user_id,
        ];
    }
}
