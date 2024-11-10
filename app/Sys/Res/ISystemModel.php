<?php

namespace App\Sys\Res;

use Illuminate\Database\Eloquent\Model;

interface ISystemModel
{
    public  function getUuid() : string;
    public  function getName() :string;
    public  function getObject() :Model;
}
