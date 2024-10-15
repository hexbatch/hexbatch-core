<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
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
        $ret =  [
            'uuid' => $this->ref_uuid,
            'username' => $this->getName(),
            'created_at' => $this->created_at_ts? Carbon::createFromTimestamp($this->created_at_ts)->toIso8601String() : null,
        ];

        return $ret;
    }
}
