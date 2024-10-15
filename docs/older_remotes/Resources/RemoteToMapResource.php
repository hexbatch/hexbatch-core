<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \Models\RemoteToMap::getConstantData()
 * @method getConstantData()
 */
class RemoteToMapResource extends JsonResource
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
            'is_secret' => $this->is_secret,
            'map_type' => $this->map_type,
            'cast_data_to_format' => $this->cast_data_to_format,
            'holder_json_path' => $this->holder_json_path,
            'remote_data_name' => $this->remote_data_name,
            'remote_data_constant' => $this->is_secret? '***': $this->getConstantData(),
            'created_at' => Carbon::createFromTimestamp($this->created_at_ts)->toIso8601String(),
        ];



        return $ret;
    }
}
