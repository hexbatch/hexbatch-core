<?php

namespace App\Api\Actions\AInterfaces;

use App\Models\HexError;

/**
 * This is given by the @see IActionLogic to the thing for processing
 */
interface IActionWorkReturn
{

    public function setHexError(HexError $error) : void;
    public function getHexError() : ?HexError;
}
