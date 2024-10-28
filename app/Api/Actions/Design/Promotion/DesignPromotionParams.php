<?php

namespace App\Api\Actions\Design\Promotion;

use App\Enums\Types\TypeOfLifecycle;
use App\Models\HexError;
use App\Models\Server;
use App\Models\UserNamespace;

class DesignPromotionParams implements IDesignPromotionParams
{
    protected ?UserNamespace $namespace = null;
    protected ?Server $server = null;
    protected ?string $uuid = null;
    protected ?string $type_name = null;
    protected bool $system = true;
    protected bool $final_type = false;

    protected TypeOfLifecycle $lifecycle = TypeOfLifecycle::PUBLISHED;


    public function setNamespace(?UserNamespace $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function setTypeName(?string $type_name): void
    {
        $this->type_name = $type_name;
    }

    public function setFinalType(bool $final_type): void
    {
        $this->final_type = $final_type;
    }

    public function getNamespace(): ?UserNamespace
    {
        return $this->namespace;
    }

    public function getServer(): ?Server {
        return $this->server;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getTypeName(): ?string
    {
        return $this->type_name;
    }

    public function isSystem(): bool
    {
        return $this->system;
    }

    public function isFinalType(): bool
    {
        return $this->final_type;
    }

    public function getLifecycle(): TypeOfLifecycle
    {
        return $this->lifecycle;
    }

    public function getHexError(): ?HexError
    {
        return null;
    }


}
