<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \App\Models\TimeBound::time_spans()
 * @method getName()
 */
class TimeBoundResource extends JsonResource
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
            'name' => $this->getName(),
            'start' => Carbon::createFromTimestamp($this->bound_start_ts)->toIso8601String(),
            'stop' => Carbon::createFromTimestamp($this->bound_stop_ts)->toIso8601String(),
            'is_retired' => $this->is_retired,
            'period_length' => $this->bound_period_length,
            'cron' => $this->bound_cron,
            'cron_timezone' => $this->bound_cron_timezone,

        ];
        if ($this->n_display_level > 1) {
            $ret['spans'] = new TimeBoundSpanCollection($this->time_spans);
        }

        return $ret;
    }
}
