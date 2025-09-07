<?php

namespace App\OpenApi;


use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\OpenApi\Things\ThingMimimalResponseTrait;
use Illuminate\Support\Collection;

abstract class ApiThingBase extends ApiCollectionBase implements IThingBaseResponse
{
    use ApiDataTrait, ThingMimimalResponseTrait;
    public function fromCollection(Collection $col, bool $do_validation = true) {

    }

    public function __construct(?Thing $thing = null)
    {
        $this->initThingFields(thing: $thing);
    }


    public  function toArray() : array  {
        return $this->getThingInfoArray()??[];
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
