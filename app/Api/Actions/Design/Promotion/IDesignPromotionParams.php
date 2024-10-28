<?php

namespace App\Api\Actions\Design\Promotion;

use App\Api\Actions\AInterfaces\IActionParams;
use App\Enums\Types\TypeOfLifecycle;

use App\Models\Server;
use App\Models\UserNamespace;

/**
 * todo try to put in the open api specs here for the params, if not then the class impelementing this
 */
interface IDesignPromotionParams extends IActionParams
{

    public function getNamespace(): ?UserNamespace;
    public function getServer(): ?Server;

    public function getUuid(): ?string;

    public function getTypeName(): ?string;

    public function isSystem(): bool;

    public function isFinalType(): bool;

    public function getLifecycle(): TypeOfLifecycle;

}
