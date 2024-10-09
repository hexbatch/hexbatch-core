<?php

namespace App\Sys\Res;

interface ISystemResource
{
    const UUID = '';
    public function onCall(): ISystemResource;
    public function onNextStep(): void;
}
