<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \App\Models\AttributeValue::getValue()
 * @method getValue()
 */
class AttributeValueResource extends JsonResource
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
            return [$this->getValue()];
        }


        $ret =  [

                'type' => $this->value_type->value,
                'min' => $this->value_numeric_min,
                'max' => $this->value_numeric_max,
                'regex' => $this->value_regex,
                'default' => empty($this->value_parent->attribute_pointer)?
                    $this->getValue() :
                    $this->value_parent->attribute_value->getValueDisplayForResource($this->n_display_level-1),
                'is_nullable' => $this->is_nullable,
        ];

        if (is_null($this->value_numeric_min)) {
            unset($ret['min']);
        }
        if (is_null($this->value_numeric_max)) {
            unset($ret['max']);
        }
        if (empty($this->value_regex)) {
            unset($ret['regex']);
        }

        return $ret;
    }
}
