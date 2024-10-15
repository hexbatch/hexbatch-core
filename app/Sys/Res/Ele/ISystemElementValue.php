<?php

namespace App\Sys\Res\Ele;

use App\Sys\Res\Atr\ISystemAttribute;

interface ISystemElementValue
{
    public function getElement() :ISystemElement;
    public function getAttribute() :ISystemAttribute;

    public function getData() : array ;

}
