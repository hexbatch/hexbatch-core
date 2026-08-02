<?php
namespace App\Sys\Res\Types\Stk\Root\Evt\Server\Traits;

use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\UserNamespace;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;

interface IServerEvent
{
    public static function makeEventTree(
        array            $children_args,
          ?ElementType   $given_type = null  ,
          ?ElementType   $other_type =null ,
          ?UserNamespace $given_namespace = null,
          ?UserNamespace $old_namespace = null,
          ?Element       $given_element = null,
          ?ElementSet    $given_set = null,
          ?Phase         $given_phase = null ,
          ?Attribute     $given_attribute = null,
          ?string        $given_uuid = null,
        ?IThangBuilder   $builder = null
    ) : Thang|IThangBuilder|null;
}
