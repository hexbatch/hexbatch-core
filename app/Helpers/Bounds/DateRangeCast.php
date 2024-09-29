<?php

namespace App\Helpers\Bounds;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DateRangeCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        preg_match('/([\[\(]{1})(.*)\,(.*)([\]\)]{1})/', $attributes['valid_range'], $matches);

        return new DateRange($matches[2], $matches[3], $matches[1], $matches[4]);
    }

    public function set($model, $key, $value, $attributes)
    {
        return [
            'valid_range' => $this->serializeRange($value)
        ];
    }

    private function serializeRange($range)
    {
        return "[" .
            optional(optional($range)->from())->toDateString() .
            "," .
            optional(optional($range)->to())->toDateString() .
            "]";
    }
}
