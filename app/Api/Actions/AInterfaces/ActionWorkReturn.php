<?php

namespace App\Api\Actions\AInterfaces;

use App\Models\HexError;


class ActionWorkReturn implements IActionWorkReturn {


    public function __construct(
        protected ?HexError $dat_error = null
    )
    {
    }



    public function getHexError(): ?HexError
    {
        return $this->dat_error;
    }

    public function setHexError(HexError $error): void
    {
        $this->dat_error = $error;
    }
}
