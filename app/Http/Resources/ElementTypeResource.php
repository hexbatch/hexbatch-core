<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method getName()
 */
class ElementTypeResource extends JsonResource
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
        if ($this->n_display_level <=0) {
            return [$this->getName()];
        }

        return [
            'type_name' => $this->getName(),
            'uuid' => $this->ref_uuid
        ];
    }
}
