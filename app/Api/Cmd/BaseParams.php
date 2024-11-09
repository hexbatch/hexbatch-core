<?php

namespace App\Api\Cmd;

use App\Exceptions\HexbatchInvalidException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Collection;

trait BaseParams
{
    public static function intRefFromCollection(Collection $collection,string $param_name) : ?int {
        $what  = (int)$collection->get($param_name);
        if (!$what)  { return null;}
        return $what;
    }

    public static function stringFromCollection(Collection $collection,string $param_name) : ?string {
        $what  = (string)$collection->get($param_name);
        if (!empty($what))  { return null;}
        return $what;
    }

    public static function unixTsFromCollection(Collection $collection,string $param_name) : ?int {
        $what  = (string)$collection->get($param_name);
        if (!empty($what))  { return null;}
        try {
            return Carbon::create($what)->unix();
        } catch (InvalidFormatException $e) {
            throw new HexbatchInvalidException(__("msg.invalid_time",['ref'=>$what]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::INVALID_TIME,$e);
        }

    }

    public static function boolFromCollection(Collection $collection,string $param_name) : ?bool {
        if (!$collection->has($param_name)) {return null;}
        return Utilities::boolishToBool($collection->get($param_name));
    }

    public static function intArrayFromCollection(Collection $collection,string $param_name) : array {
        if (!$collection->has($param_name)) {return [];}
        $meep = $collection->get($param_name,[]);
        if (!is_array($meep)) { return [$meep];}
        /** @var array  $ret_not_empty */
        $ret_not_empty = array_filter($meep, fn($value) => !empty(intval(trim($value) )) );
        return array_map(fn($value): int => intval($value), $ret_not_empty);
    }


    public static function uuidArrayFromCollection(Collection $collection,string $param_name) : array {
        if (!$collection->has($param_name)) {return [];}
        $meep = $collection->get($param_name,[]);
        if (!is_array($meep)) { return [$meep];}
        /** @var array  $ret_not_empty */
        $ret_not_empty = array_filter($meep, fn($value) => !empty(intval(trim($value) )) );

        foreach ($ret_not_empty as $tt) {
            if (!Utilities::is_uuid($tt)) {
                throw new HexbatchInvalidException(__('messages.invalid_uuid',['ref'=>$tt]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::INVALID_UUID);
            }
        }
        return $ret_not_empty;
    }

    public static function uuidFromCollection(Collection $collection,string $param_name) : ?string {
        $what  = (string)$collection->get($param_name);
        if (!empty($what))  { return null;}

        if (!Utilities::is_uuid($what)) {
            throw new HexbatchInvalidException(__('messages.invalid_uuid',['ref'=>$what]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::INVALID_UUID);
        }

        return $what;
    }

    public function makeCollection() : Collection {
        $arr = [];
        foreach ($this as $key => $val) {
            $arr[$key] = $val;
        }
        return new Collection($arr);
    }

    public function toArray() : array {
        $arr = [];
        foreach ($this as $key => $val) {
            $arr[$key] = $val;
        }
        return $arr;
    }
}
