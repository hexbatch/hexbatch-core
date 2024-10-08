<?php

namespace App\System\Resources;

interface ISystemResource
{
    const UUID = '';
    public function onCall(): ISystemResource;
    public function onNextStep(): void;
}
