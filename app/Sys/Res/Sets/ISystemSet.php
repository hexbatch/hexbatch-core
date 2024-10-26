<?php

namespace App\Sys\Res\Sets;

use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;

interface ISystemSet extends ISystemResource, ISet
{
    public static function hasEvents() :bool;

    public static function getDefiningSystemElementClass() :string|ISystemElement;

    /** @return ISystemElement[]|string[] */
    public static function getMemberSystemElementClasses() :array;



    public function getDefiningSystemElement() :?ISystemElement;



    /** @return ISystemElement[] */
    public function getSystemElements() : array ;

}
