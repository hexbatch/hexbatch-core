<?php

namespace App\OpenApi;



use Illuminate\Support\Collection;

abstract class ApiCollectionBase implements IApiParam,\JsonSerializable
{
    use ApiDataTrait;
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
