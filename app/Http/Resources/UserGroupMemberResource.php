<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \App\Models\UserGroupMember::member_user()
 * @method  getName()
 */
class UserGroupMemberResource extends JsonResource
{
    protected int $n_display_level = 1;
    public function __construct($resource, int $n_display_level = 1) {
        parent::__construct($resource);
        $this->n_display_level = $n_display_level;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          'member' => $this->n_display_level <=1 ? $this->member_user->getName():new UserResource($this->member_user,$this->n_display_level),
          'is_admin' => $this->is_admin
        ];
    }
}
