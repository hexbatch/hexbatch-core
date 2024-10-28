<?php

namespace App\Api\Actions\Design\Create;

use App\Api\Actions\AInterfaces\IDataInput;
use App\Api\Actions\AInterfaces\IParamsSystem;
use App\Enums\Types\TypeOfLifecycle;
use App\Models\UserNamespace;


class ParamsSystem implements IParamsSystem
{
    protected ?UserNamespace $namespace = null;
    protected ?string $uuid = null;
    protected ?string $type_name = null;
    protected bool $system = true;
    protected bool $final_type = false;

    protected TypeOfLifecycle $lifecycle = TypeOfLifecycle::PUBLISHED;

    public function getInputData(): IDataInput
    {
        return DataInput::createFromParamsSystem($this);
    }

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



}
