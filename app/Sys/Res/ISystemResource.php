<?php

namespace App\Sys\Res;

interface ISystemResource
{
    public static function getClassUuid() : string;
    public static function getClassName() :string;
    public function onCall(): ISystemResource;
    public function onNextStep(): void;
    public function didCreateModel(): bool;
}
