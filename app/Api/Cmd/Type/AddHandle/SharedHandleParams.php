<?php
namespace App\Api\Cmd\Type\AddHandle;

use App\Api\Cmd\BaseParams;
use Illuminate\Support\Collection;


trait SharedHandleParams
{
    use BaseParams;
    protected array $type_ids = [];
    protected ?int $handle_element_id = null;

    protected function validate() {

    }

    public function fromCollection(Collection $collection)
    {
        $this->type_ids = static::intArrayFromCollection($collection,'type_ids');
        $this->handle_element_id = static::intRefFromCollection($collection,'handle_element_id');

        $this->validate();
    }

    public function getTypeIds(): array
    {
        return $this->type_ids;
    }

    public function getHandleElementId(): ?int
    {
        return $this->handle_element_id;
    }







}
