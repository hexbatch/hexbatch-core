<?php

namespace App\Sys\Res\Sets;

use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;

interface ISystemSet extends ISystemResource, ISet
{
    public function hasEvents() :bool;
    public function getDefiningSystemElement() :?ISystemElement;



    /** @return ISystemElement[] */
    public function getSystemElements() : array ;

}
