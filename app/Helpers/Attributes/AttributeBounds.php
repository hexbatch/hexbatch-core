<?php

namespace App\Helpers\Attributes;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Attribute;
use App\Models\Enums\LocationType;
use App\Models\LocationBound;
use App\Models\TimeBound;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AttributeBounds
{

    public ?int $read_time_bounds_id;
    public ?int $write_time_bounds_id;
    public ?int $read_map_location_bounds_id;
    public ?int $write_map_location_bounds_id;
    public ?int $read_shape_location_bounds_id;
    public ?int $write_shape_location_bounds_id;


    public function __construct(Request $request)
    {

        $this->read_time_bounds_id = null;
        $this->write_time_bounds_id = null;
        $this->read_map_location_bounds_id = null;
        $this->write_map_location_bounds_id = null;
        $this->read_shape_location_bounds_id = null;
        $this->write_shape_location_bounds_id = null;

        $read_bounds = new Collection();
        $write_bounds = new Collection();
        if ($request->request->has('bounds')) {
            $bounds_block = $request->collect('bounds');
            if ($bounds_block->has('read_bounds')) {
                $read_bounds = new Collection($bounds_block->get('read_bounds'));
            }
            if ($bounds_block->has('write_bounds')) {
                $write_bounds = new Collection($bounds_block->get('write_bounds'));
            }
        }
        $all_bounds = $read_bounds->merge($write_bounds);

        foreach ($all_bounds as $key => $val) {
            $found_object = null;
            switch($key) {
                case 'read_time':
                case 'write_time':
                {
                    /** @var TimeBound $found_object */
                    $found_object = (new TimeBound())->resolveRouteBinding($val);
                    break;
                }
                case 'read_map':
                case 'write_map':
                {
                    /** @var LocationBound $found_object */
                    $found_object = (new LocationBound())->resolveRouteBinding($val);
                    if ($found_object->location_type !== LocationType::MAP) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_bounds_violation"),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    break;
                }
                case 'read_shape':
                case 'write_shape':
                {
                    /** @var LocationBound $found_object */
                    $found_object = (new LocationBound())->resolveRouteBinding($val);
                    if ($found_object->location_type !== LocationType::SHAPE) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_bounds_violation"),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    break;
                }
            } //end switch
            if (empty($found_object)) {
                continue;
            }
            $trans_key = match ($key) {
                'read_time' => 'read_time_bounds_id',
                'write_time' => 'write_time_bounds_id',
                'read_map' => 'read_map_location_bounds_id',
                'write_map' => 'write_map_location_bounds_id',
                'read_shape' => 'read_shape_location_bounds_id',
                'write_shape' => 'write_shape_location_bounds_id',

            };
            $this->$trans_key = $found_object->id;

        }
    }

    public function assign(Attribute $attribute) {
        foreach ($this as $key => $val) {
            $attribute->$key = $val;
        }
    }


}
