<?php

namespace App\Api\Actions\AInterfaces;

interface IDataOutput
{
    public function getOutputJson() : ?IOutputJson;
    public function getOutputThing() : ?IOutputThing;
}
