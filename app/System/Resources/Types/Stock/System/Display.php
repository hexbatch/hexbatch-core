<?php


namespace App\System\Resources\Types\Stock\System;

use App\System\Resources\Attributes\Stock\System\MetaData\Display\DisplayData;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Display extends BaseType
{
    const UUID = 'ef45c071-1ba1-4f3c-958d-27d8ed1bb351';
    const TYPE_NAME = 'display';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        DisplayData::UUID
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}


/*
 * todo display attributes:
  height_px
  width_px
  depth_px
  height_cm
  width_cm
  depth_cm

  weight_kg
 * color
 * primary_color
 * secondary_color
 * bg_color
 * opacity
 */


