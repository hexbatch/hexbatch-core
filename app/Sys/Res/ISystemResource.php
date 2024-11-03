<?php

namespace App\Sys\Res;

interface ISystemResource
{
    public static function getClassUuid() : string;
    public function onCall(): ISystemResource;
    public function onNextStep(): void;
}
