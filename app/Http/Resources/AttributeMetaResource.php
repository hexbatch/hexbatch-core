<?php

namespace App\Http\Resources;

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
    public function __construct($resource, int $n_display_level = 1) {
        parent::__construct($resource);
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
            if ($this->meta_iso_lang !== AttributeMetum::ANY_LANGUAGE) {
                $ret['lang'] = $this->meta_iso_lang;
            }
            return $ret;
        }
        else if($this->n_display_level === 1) {
            $ret =  [
                'type' => $this->meta_type->value,
            ];

        } else {
            $ret =  [
                'type' => $this->meta_type->value,
                'value' => $this->getMetaValue(),
            ];
        }

        if ($this->meta_iso_lang !== AttributeMetum::ANY_LANGUAGE) {
            $ret['lang'] = $this->meta_iso_lang;
        }
        if ($this->meta_mime_type) {
            $ret['mime'] = $this->meta_mime_type;
        }
        return $ret;
    }
}
