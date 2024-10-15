<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 */
class AttributeRuleResource extends JsonResource
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
            return [$this->rule_name];
        }
        else if ($this->n_display_level === 1) {
            $arr =  [
                'name' => $this->rule_name,
                'uuid' => $this->ref_uuid

            ];
        }
        else {
           $arr = [
               'name' => $this->rule_name,
               'uuid' => $this->ref_uuid
           ];

        }


        return $arr;
    }
}
