<?php

namespace App\Sys\Res\Types;
use Hexbatch\Things\Enums\TypeOfThingStatus;


trait ActionableBaseTrait
{

    protected ?TypeOfThingStatus $status = null;


    /**
     * @throws \Exception
     */
    public function runAction(array $data = []): void {

    }



}
