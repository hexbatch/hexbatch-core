<?php

namespace App\Api\Actions\AInterfaces;

use App\Models\HexError;
use App\Models\ThingResult;

interface IActionParams
{
    /**
     * todo this is given by the thing map to fill out, then sent back to the thing which gives a @see ThingResult to track
     */
    public function getHexError() : ?HexError;

}
