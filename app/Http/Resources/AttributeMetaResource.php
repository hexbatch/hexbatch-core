<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use App\Models\AttributeMetum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses AttributeMetum::getMetaValue()
 * @method getMetaValue() : mixed
 */
class AttributeMetaResource extends JsonResource
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
        if ($this->n_display_level <= 0) {
            $ret =  [
                'type' => $this->meta_type->value
            ];

            return $ret;
        }
        else if($this->n_display_level === 1) {
            $ret =  [
                'type' => $this->meta_type->value,
            ];

        } else {
            $ret =  [
                'type' => $this->meta_type->value,
                'value' => $this->meta_value,
            ];
        }

        return $ret;
    }
}
