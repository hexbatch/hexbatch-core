<?php

namespace App\Rules;

use Closure;
use GeoJson\Geometry\MultiPolygon;
use GeoJson\Geometry\Polygon;

class GeoJsonPolyReq extends GeoJsonReq
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        parent::validate($attribute,$value,$fail);
        if (!$this->geometry) {return;}


        if (!(get_class($this->geometry) === Polygon::class || get_class($this->geometry) === MultiPolygon::class) ) {
            $fail("msg.location_bound_geo_json_not_polygon")->translate();
        }

    }

}
