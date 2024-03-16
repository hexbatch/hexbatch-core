<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 */
class TimeBoundSpanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ret =  [
            'start' => Carbon::createFromTimestamp($this->span_start)->toIso8601String(),
            'stop' => Carbon::createFromTimestamp($this->span_stop)->toIso8601String(),
        ];

        return $ret;
    }
}
