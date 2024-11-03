<?php
namespace App\Api\Cmd\Element\Promote;

use Illuminate\Support\Collection;

class EleForSystem
{
    protected array $ns_owner_ids = [];
    protected array $destination_set_ids = [];
    protected ?int $number_per_set = null;
    protected array $uuids = [];

    protected ?int $phase_id = null;
    protected ?int $parent_type_id = null;


    public function makeCollection() : Collection {
        return new Collection([
            'ns_owner_ids' => $this->ns_owner_ids,
            'destination_set_ids' => $this->destination_set_ids,
            'uuids' => $this->uuids,
            'number_per_set' => $this->number_per_set,
            'phase_id' => $this->phase_id,
            'parent_type_id' => $this->parent_type_id,
        ]);
    }

    public function setNsOwnerIds(array $ns_owner_ids): EleForSystem
    {
        $this->ns_owner_ids = $ns_owner_ids;
        return $this;
    }

    public function setDestinationSetIds(array $destination_set_ids): EleForSystem
    {
        $this->destination_set_ids = $destination_set_ids;
        return $this;
    }

    public function setNumberPerSet(?int $number_per_set): EleForSystem
    {
        $this->number_per_set = $number_per_set;
        return $this;
    }

    public function setUuids(array $uuids): EleForSystem
    {
        $this->uuids = $uuids;
        return $this;
    }

    public function setPhaseId(?int $phase_id): EleForSystem
    {
        $this->phase_id = $phase_id;
        return $this;
    }

    public function setParentTypeId(?int $parent_type_id): EleForSystem
    {
        $this->parent_type_id = $parent_type_id;
        return $this;
    }



}
