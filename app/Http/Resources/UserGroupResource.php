<?php

namespace App\Http\Resources;

use App\Models\UserGroupMember;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses UserGroup::group_owner()
 * @uses UserGroup::group_members()
 * @uses UserGroup::group_admins()
 * @method UserGroupMember[]|Collection group_members()
 * @method UserGroupMember[]|Collection group_admins()
 */
class UserGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'group_name' => $this->group_name,
            'owner' =>   new UserResource($this->group_owner),
            'uuid' => $this->ref_uuid,
            'members_count' => $this->group_members()->count(),
            'admins_count' => $this->group_admins()->count(),
            'is_admin' => $this->whenNotNull($this->is_admin),
            'is_owner' => auth()->user()->id === $this->user_id,
        ];
    }
}
