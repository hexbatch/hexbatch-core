<?php

namespace App\Sys\Res\Ele;

use App\Models\Element;

interface IElement
{


    public function getElementObject() : ?Element;
}
