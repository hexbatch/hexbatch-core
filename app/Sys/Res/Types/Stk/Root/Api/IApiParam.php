<?php

namespace App\Sys\Res\Types\Stk\Root\Api;

use Illuminate\Support\Collection;

interface IApiParam
{
    public function fromCollection(Collection $col, bool $do_validation = true);
    public function toCollection(Collection $col) : Collection;
    public function toArray() : array ;
}
