<?php
namespace App\Api\Cmd\Design\PublishPromote;

use App\Api\Cmd\BaseParams;
use App\Enums\Types\TypeOfLifecycle;
use Illuminate\Support\Collection;

trait PublishingParams
{
    use BaseParams;
    protected ?int $type_id = null;
    protected array $parent_ids = [];

    protected ?TypeOfLifecycle $lifecycle =null;

    protected function validate() {

        if (!$this->type_id) {
            throw new \LogicException("type id must be set");
        }
    }

    public function fromCollection(Collection $collection)
    {
        $this->type_id = static::intRefFromCollection($collection,'type_id');
        $this->parent_ids = static::intArrayFromCollection($collection,'parent_ids');
        $this->lifecycle = TypeOfLifecycle::getFromCollection($collection,'lifecycle');

        $this->validate();
    }

    public function getTypeId(): ?int
    {
        return $this->type_id;
    }

    public function getParentIds(): array
    {
        return $this->parent_ids;
    }

    public function getLifecycle(): ?TypeOfLifecycle
    {
        return $this->lifecycle;
    }


}
