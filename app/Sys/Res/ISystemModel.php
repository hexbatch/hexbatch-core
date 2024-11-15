<?php

namespace App\Sys\Res;

interface ISystemModel
{
    public  function getUuid() : string;
    public  function getName() :string;
}
