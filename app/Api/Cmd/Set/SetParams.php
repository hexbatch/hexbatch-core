<?php
namespace App\Api\Cmd\Set;

use App\Api\Cmd\BaseParams;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use Illuminate\Support\Collection;

trait SetParams
{
    use BaseParams;

    protected ?int $parent_set_element_id = null;
    protected ?string $uuid = null;
    protected ?bool $has_events = null;
    protected array $content_element_ids = [];
    protected function validate() {
        if (!$this->parent_set_element_id) {
            throw new HexbatchNotPossibleException(__('msg.set_must_have_a_defining_element'),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::SET_SCHEMA_ISSUE);

        }
    }

    public function fromCollection(Collection $collection)
    {
        $this->parent_set_element_id = static::intRefFromCollection($collection,'parent_set_element_id');
        $this->uuid = static::uuidFromCollection($collection,'uuid');
        $this->has_events = static::boolFromCollection($collection,'has_events');
        $this->content_element_ids = static::intArrayFromCollection($collection,'content_element_ids');

        $this->validate();
    }

}
