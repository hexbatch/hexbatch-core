<?php


namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Atr\Stk\MetaData\Display\DisplayData;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Display extends BaseType
{
    const UUID = 'ef45c071-1ba1-4f3c-958d-27d8ed1bb351';
    const TYPE_NAME = 'display';



    const ATTRIBUTE_CLASSES = [
        DisplayData::class,
        DisplayData\HeightCm::class,
        DisplayData\WidthCm::class,
        DisplayData\DepthCm::class,
        DisplayData\HeightPx::class,
        DisplayData\WidthPx::class,
        DisplayData\DepthPx::class,
        DisplayData\WeightKg::class,
        DisplayData\Color::class,
        DisplayData\BackgroundColor::class,
        DisplayData\PrimaryColor::class,
        DisplayData\SecondaryColor::class,
        DisplayData\SkinUrl::class,
        DisplayData\Opacity::class,
    ];

    const PARENT_CLASSES = [
        Root::class
    ];

}




