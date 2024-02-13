<?php

namespace App\Rules;

use Closure;
use GeoJson\GeoJson;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeoJsonReq implements ValidationRule
{
    protected ?GeoJson $geometry = null;
    /**
     * Run the validation rule.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $this->geometry = null;
        try {
            if (is_string($value)) {
                // Handle undecoded JSON
                $value = json_decode($value);
                if (is_null($value)) {
                    $fail("msg.location_bound_json_invalid")->translate();
                    return;
                }
            }
            // An exception will be thrown if parsing fails
            $this->geometry = GeoJson::jsonUnserialize($value);
        } catch (Throwable $t) {
            Log::warning("Cannot covert to geojson: ". $t->getMessage());
            $fail("msg.location_bound_json_invalid_geo_json")->translate(['msg'=>$t->getMessage()]);
            return;
        }

    }

}
