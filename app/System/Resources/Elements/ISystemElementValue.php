<?php

namespace App\System\Resources\Elements;

use App\System\Resources\Attributes\ISystemAttribute;

interface ISystemElementValue
{
    public function getElement() :ISystemElement;
    public function getAttribute() :ISystemAttribute;

    public function getData() : array ;

}
