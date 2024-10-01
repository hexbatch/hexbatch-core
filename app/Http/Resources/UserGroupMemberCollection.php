<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @uses \App\Models\UserNamespaceMember::parent_namespace()
 */
class UserGroupMemberCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'group' => new UserGroupResource($this->collection->first()?->parent_group),
            'members' => $this->collection
        ];
    }
}
