<?php

namespace App\Api\Actions\Design\Create;

use App\Api\Actions\AInterfaces\IOutputJson;

class OutputJson implements IOutputJson
{

    public function toJsonArray() : array {
        return [];
    }
}
