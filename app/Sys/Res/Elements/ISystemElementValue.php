<?php

namespace App\Sys\Res\Elements;

use App\Sys\Res\Attributes\ISystemAttribute;

interface ISystemElementValue
{
    public function getElement() :ISystemElement;
    public function getAttribute() :ISystemAttribute;

    public function getData() : array ;

}
