<?php


namespace App\Sys\Res\Types\Stock\System;

use App\Sys\Res\Attributes\Stock\System\MetaData\Display\DisplayData;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Display extends BaseType
{
    const UUID = 'ef45c071-1ba1-4f3c-958d-27d8ed1bb351';
    const TYPE_NAME = 'display';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        DisplayData::UUID,
        DisplayData\HeightCm::UUID,
        DisplayData\WidthCm::UUID,
        DisplayData\DepthCm::UUID,
        DisplayData\HeightPx::UUID,
        DisplayData\WidthPx::UUID,
        DisplayData\DepthPx::UUID,
        DisplayData\WeightKg::UUID,
        DisplayData\Color::UUID,
        DisplayData\BackgroundColor::UUID,
        DisplayData\PrimaryColor::UUID,
        DisplayData\SecondaryColor::UUID,
        DisplayData\SkinUrl::UUID,
        DisplayData\Opacity::UUID,
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}




