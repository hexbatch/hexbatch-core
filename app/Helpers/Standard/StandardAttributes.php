<?php

namespace App\Helpers\Standard;


use App\Exceptions\HexbatchInvalidException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;



class StandardAttributes
{


    public static function validateMapLocation($what, bool $b_throw_exception = true): bool
    {
        $b_ok = true;
        $maybe_coordination = Utilities::toArrayOrNull($what);
        if (!$maybe_coordination) {
            $b_ok = false;
        }
        if ($b_ok) {
            if (
                !array_key_exists('latitude', $maybe_coordination)
                || !array_key_exists('longitude', $maybe_coordination)
                || !is_numeric($maybe_coordination['longitude'] )|| !is_numeric($maybe_coordination['latitude'] )
                || ($maybe_coordination['longitude'] > 180 || $maybe_coordination['longitude'] < -180)
                || ($maybe_coordination['latitude'] > 90 || $maybe_coordination['latitude'] < -90)
            ) {
                $b_ok = false;
            }
        }

        if (!$b_ok) {
            if ($b_throw_exception) {
                throw new HexbatchInvalidException(__("msg.not_map_coordinate"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::MAP_COORDINATE_ISSUE);
            }
            return false;
        }
        return true;
    }

    public static function validateShapeLocation($what, bool $b_throw_exception = true): bool
    {
        $b_ok = true;
        $maybe_coordination = Utilities::toArrayOrNull($what);
        if (!$maybe_coordination) {
            $b_ok = false;
        }
        if ($b_ok) {
            if (
                !array_key_exists('x',$maybe_coordination )
                || !array_key_exists('y',$maybe_coordination )
                || !array_key_exists('z',$maybe_coordination )
                || !is_numeric($maybe_coordination['x'] )|| !is_numeric($maybe_coordination['y'] ) || !is_numeric($maybe_coordination['z'] )
            ) {
                $b_ok = false;
            }
        }

        if (!$b_ok) {
            if ($b_throw_exception) {
                throw new HexbatchInvalidException(__("msg.not_shape_coordinate"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::SHAPE_COORDINATE_ISSUE);
            }
            return false;
        }
        return true;
    }


}
