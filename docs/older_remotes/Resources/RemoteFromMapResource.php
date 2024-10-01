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
class RemoteFromMapResource extends JsonResource
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
            'map_type' => $this->map_type,
            'remote_json_path' => $this->remote_json_path,
            'remote_regex_match' => $this->remote_regex_match,
            'remote_data_name' => $this->remote_data_name,
            'created_at' => Carbon::createFromTimestamp($this->created_at_ts)->toIso8601String(),
        ];



        return $ret;
    }
}
