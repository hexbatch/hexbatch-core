<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \App\Models\Remote::getName()
 * @method getName()
 */
class RemoteMetaResource extends JsonResource
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
            return [];
        }

        $ret =  [
            'time_bounds' => $this->remote_time_bounds_id? new TimeBoundResource($this->remote_meta_time_bound,null,$this->n_display_level - 1): null ,
            'map_bounds' => $this->remote_map_bounds_id? new LocationBoundResource($this->remote_meta_map_bound,null,$this->n_display_level - 1): null ,
            'icu_locale_codes' => $this->remote_icu_locale_codes,
            'terms_of_use_link' => $this->remote_terms_of_use_link,
            'privacy_link' => $this->remote_privacy_link,
            'about_link' => $this->remote_about_link,
            'description' => $this->remote_description,
            'created_at' => Carbon::createFromTimestamp($this->created_at_ts)->toIso8601String(),
            'updated_at' => $this->updated_at_ts? Carbon::createFromTimestamp($this->updated_at_ts)->toIso8601String() : null ,
        ];



        return $ret;
    }
}
