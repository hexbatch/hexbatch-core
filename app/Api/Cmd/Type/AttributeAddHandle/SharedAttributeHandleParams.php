<?php
namespace App\Api\Cmd\Type\AttributeAddHandle;

use App\Api\BaseParams;
use Illuminate\Support\Collection;


trait SharedAttributeHandleParams
{
    use BaseParams;
    protected array $attribute_ids = [];
    protected ?int $handle_attribute_id = null;

    protected function validate() {

    }

    public function fromCollection(Collection $collection)
    {
        $this->attribute_ids = static::intArrayFromCollection($collection,'attribute_ids');
        $this->handle_attribute_id = static::intRefFromCollection($collection,'handle_attribute_id');

        $this->validate();
    }

    /** @return int[] */
    public function getAttributeIds(): array
    {
        return $this->attribute_ids;
    }

    public function getHandleAttributeId(): ?int
    {
        return $this->handle_attribute_id;
    }









}
