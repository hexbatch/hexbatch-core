<?php

namespace App\Helpers\Bounds;


use App\Enums\Bounds\LocationType;
use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\LocationBound;
use App\Rules\BoundNameReq;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LocationBoundGathering
{
    public ?LocationBound $current_bound;
    public ?array $geo_json =null;

    public ?string $bound_name =null;

    public ?LocationType $location_type = null;



    public function __construct(Collection $request,?LocationBound $current_bound = null,?LocationType $location_type = null )
    {
        $this->current_bound = $current_bound;

        if ($request->has('bound_name')) {
            $test  = $request->get('bound_name');
            if (is_string($test) && Str::trim($test)) {
                $this->bound_name = Str::trim($test);
                $this->validateName($this->bound_name);
            }
        }

        if ($location_type) {
            $this->location_type = $location_type;
        } else {
            if ( $request->has('location_type')) {
                $test_string = $request->get('location_type');
                $this->location_type  = LocationType::tryFrom($test_string);
                if (!$this->location_type ) {
                    throw new HexbatchNotPossibleException(__("msg.location_bounds_has_wrong_type",['bad_type'=>$test_string]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::BOUND_NEEDS_MIN_INFO);
                }
            }
        }

        if ($request->has('geo_json')) {
            $what_geo =  $request->get('geo_json');
            if (is_array($what_geo) && !empty($what_geo)) {
                $this->geo_json = $what_geo;
            }
        }

        if (!$current_bound && (!$this->bound_name || !$this->geo_json || !$this->location_type) ) {
            throw new HexbatchCoreException(__("msg.location_bounds_needs_minimum_info"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_NEEDS_MIN_INFO);
        }

    }

    public function assign() : LocationBound {
        if (!$this->current_bound) {
            $this->current_bound = new LocationBound();
        }
        $node = $this->current_bound;

        if ($this->bound_name !== null ) { $node->bound_name = $this->bound_name; }
        if ($this->location_type !== null ) { $node->location_type = $this->location_type; }

        if ($this->location_type && $this->geo_json) {
            $node->setShape(json_encode($this->geo_json),$this->location_type);
        }

        $node->save();

        $out = LocationBound::buildLocationBound(id: $node->id)->first();
        return $out;
    }

    protected function validateName(?string $name) {
        if (!$name) {return;}
        try {
            Validator::make(['location_bound_name' => $name], [
                'location_bound_name' => ['required', 'string', new BoundNameReq()],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_INVALID_NAME);
        }
    }
}
