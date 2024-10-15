<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use App\Models\UserNamespace;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \App\Models\UserNamespaceMember::namespace_member()
 * @property UserNamespace  namespace_member
 */
class UserNamespaceMemberResource extends JsonResource
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
        return [
          'member' => $this->n_display_level <=1 ? $this->namespace_member->getName():new UserNamespaceResource($this->namespace_member,null,$this->n_display_level),
          'is_admin' => $this->is_admin
        ];
    }
}
