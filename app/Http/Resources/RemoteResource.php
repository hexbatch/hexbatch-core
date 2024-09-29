<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \Models\Remote::getName()
 * @method getName()
 */
class RemoteResource extends JsonResource
{
    protected int $n_display_level = 1;
    public function __construct($resource, mixed $unused = null,int $n_display_level = 0) {
        parent::__construct($resource);
        Utilities::ignoreVar($unused);
        $this->n_display_level = $n_display_level;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request) : array
    {
        if ($this->n_display_level <=0) {
            return [
                'uuid' => $this->ref_uuid,
                'name' => $this->remote_name,
                'owner' => $this->remote_owner->getName(),
                "uri_type"=>$this->uri_type,
                "remote_uri_main"=>$this->remote_uri_main
            ];
        }



        $ret =  [
            'uuid' => $this->ref_uuid,
            'name' => $this->remote_name,
            'is_retired' => $this->is_retired,
            'is_on' => $this->is_on,
            'created_at' => Carbon::createFromTimestamp($this->created_at_ts)->toIso8601String(),
            'owner' => new UserResource($this->remote_owner,null,$this->n_display_level - 1),
            'cache' => [
                "is_caching" => $this->is_caching,
                "is_using_cache_on_failure" => $this->is_using_cache_on_failure,
                "cache_ttl_seconds" =>  $this->cache_ttl_seconds,
                "cache_keys" => $this->cache_keys,
            ],

            "call_schedule" => [
                "rate_limit_max_per_unit"=>$this->rate_limit_max_per_unit,
                "rate_limit_unit_in_seconds"=>$this->rate_limit_unit_in_seconds,
                "max_concurrent_calls"=> $this->max_concurrent_calls,
                "rate_limit_count"=> $this->rate_limit_count,
                'rate_limit_starts_at' => $this->rate_limit_starts_at ? Carbon::createFromTimestamp($this->rate_limit_starts_at)->toIso8601String() : null ,
            ],

            "uri" => [
                "uri_type"=>$this->uri_type,
                "uri_method"=>$this->uri_method_type,
                "uri_protocol"=>$this->uri_protocol,
                "uri_port"=>$this->uri_port,
                "uri_to_remote_format"=>$this->to_remote_format,
                "uri_from_remote_format"=>$this->from_remote_format,
                "remote_uri_main"=>$this->remote_uri_main,
                "remote_uri_path"=>$this->remote_uri_path
            ]
        ];

        if ($this->usage_group &&  $this->n_display_level > 1 ) {
            $ret['usage_group'] = new UserGroupResource($this->usage_group,null,$this->n_display_level - 1);
        }

        if ($this->meta_of_remote &&  $this->n_display_level > 1 ) {
            $ret['meta'] = new RemoteMetaResource($this->meta_of_remote,$this->n_display_level - 1);
        }

        if ($this->n_display_level > 1) {

            $ret['data'] = [
                'from_remote_map' => [],
                'to_remote_map' => [],
            ];

            foreach ($this->rules_to_remote as $to_map) {
                $ret['data']['to_remote_map'][] = new RemoteToMapResource($to_map);
            }

            foreach ($this->rules_from_remote as $from_map) {
                $ret['data']['from_remote_map'][] = new RemoteFromMapResource($from_map);
            }
        }



        return $ret;
    }
}
