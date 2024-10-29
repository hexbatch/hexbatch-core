<?php
namespace App\Api\Cmd\Design\Promote;

use App\Api\Cmd\IActionParams;
use App\Enums\Types\TypeOfLifecycle;
use App\Models\Server;
use App\Models\Thing;
use App\Models\UserNamespace;

class DesignPromoteParams implements IActionParams
{

    protected ?UserNamespace $namespace = null;
    protected ?Server $server = null;

    protected ?string $uuid = null;
    protected ?string $type_name = null;
    protected bool $system = true;
    protected bool $final_type = false;

    protected TypeOfLifecycle $lifecycle = TypeOfLifecycle::PUBLISHED;

    public function pullData(Thing $thing): void
    {
        // todo pull the data from the thing data action-setup rows
    }

    public function getNamespace(): ?UserNamespace
    {
        return $this->namespace;
    }

    public function getServer(): ?Server
    {
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


}
