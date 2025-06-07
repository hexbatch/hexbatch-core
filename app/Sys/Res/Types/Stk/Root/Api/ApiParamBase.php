<?php

namespace App\Sys\Res\Types\Stk\Root\Api;

use Illuminate\Support\Collection;

abstract class ApiParamBase implements IApiParam,\JsonSerializable
{
    public function fromCollection(Collection $col, bool $do_validation = true) {

    }


    public  function toArray() : array  {
        return [];
    }



    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toCollection(Collection $col): Collection
    {
        return new Collection($this->toArray());
    }

}
