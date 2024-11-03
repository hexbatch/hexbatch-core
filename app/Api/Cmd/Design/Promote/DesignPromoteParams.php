<?php
namespace App\Api\Cmd\Design\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;

use App\Enums\Types\TypeOfLifecycle;

use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignPromotion;
use Illuminate\Support\Collection;

class DesignPromoteParams extends DesignPromotion implements IActionParams,IActionOaInput
{

    protected ?int $namespace_id = null;
    protected ?int $server_id = null;

    protected ?string $uuid = null;
    protected ?string $type_name = null;
    protected bool $system = true;
    protected bool $final_type = false;

    protected TypeOfLifecycle $lifecycle = TypeOfLifecycle::PUBLISHED;




    public function fromThing(Thing $thing): void
    {
        // todo pull the data from the thing and fill in the data here from the json stored there
    }

    public function fromCollection(Collection $collection)
    {
        $this->namespace_id = (int)$collection->get('namespace_id');
        if (!$this->namespace_id)  {$this->namespace_id = null;}

        $this->server_id = (int)$collection->get('server_id');
        if (!$this->server_id)  {$this->server_id = null;}

        $this->uuid = (string)$collection->get('uuid');
        if (empty($this->uuid)) {$this->uuid = null;}

        $this->type_name = (string)$collection->get('type_name');

        if ($collection->has('system')) {
            $this->system = (bool)$collection->get('system');
        }

        if ($collection->has('final_type')) {
            $this->final_type = (bool)$collection->get('final_type');
        }

        if ($collection->has('lifecycle')) {
            $this->lifecycle = TypeOfLifecycle::tryFromInput($collection->get('lifecycle'));
        }

    }

    public function toArray() : array {
        return [];
    }


    public function getNamespaceId(): ?int
    {
        return $this->namespace_id;
    }

    public function getServerId(): ?int
    {
        return $this->server_id;
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
