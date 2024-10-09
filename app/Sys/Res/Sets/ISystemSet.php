<?php

namespace App\Sys\Res\Sets;

use App\Models\ElementSet;
use App\Sys\Res\Elements\ISystemElement;
use App\Sys\Res\ISystemResource;

interface ISystemSet extends ISystemResource
{
    public function getSetUuid() :string;
    public function hasEvents() :bool;
    public function getDefiningElement() :?ISystemElement;
    public function getSetObject() :?ElementSet;


    /** @return ISystemElement[] */
    public function getElements() : array ;

}
