<?php
namespace App\Api\Cmd\Element\PromoteEdit;

use App\Api\Cmd\BaseParams;
use Illuminate\Support\Collection;


trait EditElementParams
{
    use BaseParams;

    protected array $element_ids = [];
    protected ?int $phase_id = null;
    protected ?int $set_id = null;
    protected ?int $owning_namespace_id = null;

    protected ?bool $system = true;

    protected function validate() {

    }

    public function fromCollection(Collection $collection)
    {

        $this->element_ids = static::intArrayFromCollection($collection,'element_ids');
        $this->phase_id = static::intRefFromCollection($collection,'phase_id');
        $this->set_id = static::intRefFromCollection($collection,'set_id');


        $this->validate();

    }

    public function getElementIds(): array
    {
        return $this->element_ids;
    }

    public function getPhaseId(): ?int
    {
        return $this->phase_id;
    }

    public function getSetId(): ?int
    {
        return $this->set_id;
    }

    public function getOwningNamespaceId(): ?int
    {
        return $this->owning_namespace_id;
    }
    public function isSystem(): ?bool
    {
        return $this->system;
    }


}
