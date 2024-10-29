<?php
namespace App\Api\Cmd\Design\Promote;

use App\Api\Cmd\IActionReturn;
use App\Models\ElementType;
use App\Models\HexError;
use App\Models\Thing;

class DesignPromoteReturn implements IActionReturn
{


    public function __construct(
        protected ElementType $new_type,
        protected ?HexError $hex_error = null
    )
    {
    }

    public function writeData(Thing $thing)
    {
        // todo write the new type id into the thing data for the thing parent to process
    }

    public function getHexError(): ?HexError
    {
        return $this->hex_error;
    }
}
