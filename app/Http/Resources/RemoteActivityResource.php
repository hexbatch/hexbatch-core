<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use App\Models\RemoteActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RemoteActivity
 */
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
            'status'        => $this->remote_activity_status_type->value,
            'started_at' => Carbon::createFromTimestamp($this->created_at_ts)->toIso8601String()
        ];

        if ($this->remote_call_ended_at_ts) {
            $ret['ended_at'] = Carbon::createFromTimestamp($this->remote_call_ended_at_ts)->toIso8601String();
        }
        if ($this->n_display_level === 1) {
            return $ret;
        }
        $ret =  [
            'uuid'      => $this->ref_uuid,
            'status'    => $this->remote_activity_status_type->value,
            'started_at'=> Carbon::createFromTimestamp($this->created_at_ts)->toIso8601String(),
            'ended_at'  => $this->remote_call_ended_at_ts? Carbon::createFromTimestamp($this->remote_call_ended_at_ts)->toIso8601String(): null ,

            'callers' => [
                'stack' => $this->caller_action ? new UserGroupResource($this->caller_action,null,$this->n_display_level - 1) : null,
                'attribute' => $this->caller_attribute ? new AttributeResource($this->caller_attribute,null,$this->n_display_level - 1) : null,
                'user' => $this->caller_user ? new UserResource($this->caller_user,null,$this->n_display_level - 1) : null,
                'server' => $this->caller_server ? new ServerResource($this->caller_server,null,$this->n_display_level - 1) : null,
                'element' => $this->caller_element ? new ElementResource($this->caller_element,null,$this->n_display_level - 1) : null,
                'type' => $this->caller_type ? new ElementTypeResource($this->caller_type,null,$this->n_display_level - 1) : null,
            ],
            'cache' => [
              'status' => $this->cache_status_type->value  ,
              'policy' => $this->cache_policy_type->value
            ],

            'input_data' => [
                'to_remote_processed_data'=> $this->to_remote_processed_data,
                'to_remote_files'=> $this->to_remote_files,
                'to_headers'=> $this->to_headers,
            ],

            'output_data' => [
                'response_code'=> $this->response_code,
                'from_remote_processed_data'=> $this->from_remote_processed_data,
                'from_remote_raw_text'=> $this->from_remote_raw_text,
                'from_headers'=> $this->from_headers,
            ],

            'errors' => $this->errors,

            'consumer_passthrough_data' => $this->consumer_passthrough_data,

            'remote' => new RemoteResource($this->remote_parent,null,$this->n_display_level - 1)
        ];

        if ($this->n_display_level > 2) {
            $ret['callers']['location_geo_json'] = !empty($this->location_geo_json) ? $this->location_geo_json : null;
        }

        if ($this->n_display_level > 2) {
            $ret['stack'] = !empty($this->remote_stack_id) ? new RemoteStackResource($this->home_stack,$this->n_display_level - 1) : null;
        }

        return $ret;
    }
}
