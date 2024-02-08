<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses User::user_element()
 * @method getName()
 */
class UserResource extends JsonResource
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
        if ($this->n_display_level <=0) {
            return [$this->getName()];
        }
        return [
            'uuid' => $this->ref_uuid,
            'username' => $this->username,
            'type' => new ElementTypeResource($this->user_type,null,$this->n_display_level - 1),
            'element' => new ElementResource($this->user_element,null,$this->n_display_level - 1),
            'group' => new UserGroupResource($this->user_group,null,$this->n_display_level - 1),
            'created_at' => $this->created_at_ts
        ];
    }
}
