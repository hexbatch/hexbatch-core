<?php

namespace App\System\Resources\Sets;

use App\System\Resources\Elements\ISystemElement;
use App\System\Resources\ISystemResource;

interface ISystemSet extends ISystemResource
{
    public function getSetUuid() :string;
    public function hasEvents() :bool;
    public function getDefiningElement() :ISystemElement;


    /** @return ISystemElement[] */
    public function getElements() : array ;

}
