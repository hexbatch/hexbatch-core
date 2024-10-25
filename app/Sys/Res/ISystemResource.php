<?php

namespace App\Sys\Res;

interface ISystemResource
{
    public static function getUuid() : string;
    public function onCall(): ISystemResource;
    public function onNextStep(): void;
}
