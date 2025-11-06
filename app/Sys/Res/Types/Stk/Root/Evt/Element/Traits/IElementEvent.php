<?php
namespace App\Sys\Res\Types\Stk\Root\Evt\Element\Traits;

use App\Models\Element;
use App\Models\ElementSet;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;

interface IElementEvent
{
    public static function callEventTree(
        Element                $given_element,
        ?ElementSet             $given_set,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder|null;
}
