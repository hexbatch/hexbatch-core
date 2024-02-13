<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class RemoteActivityResource extends JsonResource
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
            return [$this->ref_uuid];
        }
        $ret =  [
            'remote' => $this->remote_parent->getName(),
            'uuid'          => $this->ref_uuid,
            'status'        => $this->remote_status_type->value,
            'started_at_ts' => $this->created_at_ts
        ];

        if ($this->remote_call_ended_at_ts) {
            $ret['ended_at_ts'] = $this->remote_call_ended_at_ts;
        }
        if ($this->n_display_level === 1) {
            return $ret;
        }

        $ret['remote'] = new RemoteResource($this->remote_parent,null,$this->n_display_level - 1);

        return $ret;
    }
}
