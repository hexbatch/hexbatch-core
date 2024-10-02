<?php

namespace App\Helpers\Types;

use App\Enums\Attributes\AttributePingType;

use App\Exceptions\HexbatchNotPossibleException;

use App\Exceptions\RefCodes;
use App\Helpers\Attributes\AttributeGathering;


use App\Models\ElementType;

use App\Models\User;

use Illuminate\Http\Request;


class TypeGathering
{

    public static function doPing(Request $request,ElementType $type,AttributePingType $attribute_ping_type) : array
    {
        $ret = [];
        foreach ($type->type_attributes as $attribute) {
            $ret[$attribute->getName()] = AttributeGathering::doPing($request,$attribute,$attribute_ping_type);
        }



        $user_lookup = $request->get('user');

        /** @var User $check_user */
        $check_user = null;
        if ($user_lookup) {
            $check_user = (new User)->resolveRouteBinding($user_lookup);
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_USER ||
            $attribute_ping_type === AttributePingType::READ || $attribute_ping_type === AttributePingType::READ_USER) {
            if (!$check_user) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }

            if($type->read_whitelist_group) {
                $ret['read'] = (bool)$type->read_whitelist_group->isMember($check_user->id);
            } else {
                $ret['read'] = true;
            }

        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_USER
            || $attribute_ping_type === AttributePingType::WRITE || $attribute_ping_type === AttributePingType::WRITE_USER) {
            if (!$check_user) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }

            if($type->write_whitelist_group) {
                $ret['write'] = (bool)$type->write_whitelist_group->isMember($check_user->id);
            } else {
                $ret['write'] = true;
            }

        }

        foreach ($type->type_parents as $parent) {
            $ret[$parent->getName()] = TypeGathering::doPing($request,$type,$attribute_ping_type);
        }

        return $ret;
    }



}
