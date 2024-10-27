<?php

namespace App\Api\Actions\Design\Create;

use App\Api\Actions\AInterfaces\IDataOutput;
use App\Api\Actions\AInterfaces\IOutputJson;

class DesignCreateOutputJson implements IOutputJson
{

    public function toJsonArray() : array {
        return [];
    }
}
