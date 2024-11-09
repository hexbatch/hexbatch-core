<?php
namespace App\Api\Cmd\Phase;

use App\Api\Cmd\BaseParams;
use Illuminate\Support\Collection;


trait PhaseParams
{
    use BaseParams;

    protected ?int $phase_id = null;
    protected ?int $phase_type_id = null;
    protected ?int $edited_by_phase_id = null;
    protected ?bool $default_phase = null;

    protected ?string $uuid = null;



    protected function validate() {
    }

    public function fromCollection(Collection $collection)
    {
        $this->phase_id = static::intRefFromCollection($collection,'phase_id');
        $this->phase_type_id = static::intRefFromCollection($collection,'phase_type_id');
        $this->edited_by_phase_id = static::intRefFromCollection($collection,'edited_by_phase_id');
        $this->uuid = static::uuidFromCollection($collection,'uuid');
        $this->default_phase = static::boolFromCollection($collection,'default_phase');

        $this->validate();
    }

    public function getPhaseId(): ?int
    {
        return $this->phase_id;
    }

    public function getPhaseTypeId(): ?int
    {
        return $this->phase_type_id;
    }

    public function getEditedByPhaseId(): ?int
    {
        return $this->edited_by_phase_id;
    }

    public function isDefaultPhase(): ?bool
    {
        return $this->default_phase;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }








}
