<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses User::user_element()
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
            'start' => $this->span_start,
            'stop' => $this->span_stop,
        ];

        if ($request->query->getString('tz')) {
            $ret['alt'] = [
                'start' => Carbon::createFromTimestamp($this->span_start,$request->query->getString('tz'))->toIso8601String(),
                'stop' => Carbon::createFromTimestamp($this->span_stop,$request->query->getString('tz'))->toIso8601String(),
            ];
        }
        return $ret;
    }
}
