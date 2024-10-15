<?php

namespace App\Sys\Res\Sets;


use App\Models\ElementSet;


interface ISet
{

    public function getSetObject() :?ElementSet;

}
