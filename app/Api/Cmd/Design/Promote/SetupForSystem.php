<?php
namespace App\Api\Cmd\Design\Promote;

use App\Enums\Types\TypeOfLifecycle;
use Illuminate\Support\Collection;

class SetupForSystem
{
    protected ?int $namespace_id = null;
    protected ?int $server_id = null;

    protected ?string $uuid = null;

    protected ?string $type_name = null;
    protected bool $system = true;
    protected bool $final_type = false;

    protected TypeOfLifecycle $lifecycle = TypeOfLifecycle::PUBLISHED;

    public function makeCollection() : Collection {
        return new Collection([
            'namespace_id' => $this->namespace_id,
            'server_id' => $this->server_id,
            'uuid' => $this->uuid,
            'type_name' => $this->type_name,
            'system' => $this->system,
            'final_type' => $this->final_type,
            'lifecycle' => $this->lifecycle->value,
        ]);
    }

    public function setNamespaceId(?int $namespace_id): SetupForSystem
    {
        $this->namespace_id = $namespace_id;
        return $this;
    }

    public function setServerId(?int $server_id): SetupForSystem
    {
        $this->server_id = $server_id;
        return $this;
    }

    public function setUuid(?string $uuid): SetupForSystem
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setTypeName(?string $type_name): SetupForSystem
    {
        $this->type_name = $type_name;
        return $this;
    }

    public function setSystem(bool $system): SetupForSystem
    {
        $this->system = $system;
        return $this;
    }

    public function setFinalType(bool $final_type): SetupForSystem
    {
        $this->final_type = $final_type;
        return $this;
    }

    public function setLifecycle(TypeOfLifecycle $lifecycle): SetupForSystem
    {
        $this->lifecycle = $lifecycle;
        return $this;
    }


}
