<?php

namespace App\Http\Resources;

use App\Models\AttributeMetum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses User::user_element()
 */
class AttributeMetaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $arr =  [
            'type' => $this->ref_uuid,
            'value' => $this->ref_uuid,
        ];
        if ($this->meta_iso_lang !== AttributeMetum::ANY_LANGUAGE) {
            $arr['lang'] = $this->meta_iso_lang;
        }
        if ($this->meta_mime_type) {
            $arr['mime'] = $this->meta_mime_type;
        }
        return $arr;
    }
}
