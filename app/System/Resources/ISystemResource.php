<?php

namespace App\System\Resources;

interface ISystemResource
{
    public function onCall(): ISystemResource;
}
