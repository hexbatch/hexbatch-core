<?php
namespace App\Api\Cmd\Element;

use App\Api\Cmd\BaseParams;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use Illuminate\Support\Collection;


trait BulkElementParams
{
    use BaseParams;

    const NO_SETS_MADE_YET_STUB_ID = -1;

    protected array $ns_owner_ids = [];
    protected array $destination_set_ids = [];
    protected ?int $number_per_set = null;
    protected array $uuids = [];

    protected ?int $phase_id = null;
    protected ?int $parent_type_id = null;

    protected ?bool $system = true;

    protected function validate() {
        if (empty($this->ns_owner_ids)) {
            throw new HexbatchNotPossibleException(
                __('msg.elements_must_have_owner'),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::ELEMENT_BAD_SCHEMA
            );
        }

        if (empty($this->parent_type_id)) {
            throw new HexbatchNotPossibleException(
                __('msg.elements_must_have_type'),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::ELEMENT_BAD_SCHEMA
            );
        }

        //test for the correct array sizes
        if (count($this->uuids)) {
            $total_uuids_expected =
                count($this->destination_set_ids)
                * count($this->ns_owner_ids)
                * $this->number_per_set;

            if (count($this->uuids) !== $total_uuids_expected ) {
                throw new \LogicException("The count of the uuid is not the expected size");
            }
        }
    }

    public function fromCollection(Collection $collection)
    {


        $this->ns_owner_ids = static::intArrayFromCollection($collection,'ns_owner_ids');
        $this->destination_set_ids = static::intArrayFromCollection($collection,'destination_set_ids');
        $this->uuids  = static::uuidArrayFromCollection($collection,'uuids');

        $this->number_per_set = (int)static::intRefFromCollection($collection,'number_per_set');
        if ($this->number_per_set < 1) { $this->number_per_set = 1; }

        $this->phase_id = static::intRefFromCollection($collection,'phase_id');
        $this->parent_type_id = static::intRefFromCollection($collection,'parent_type_id');
        $this->system = static::boolFromCollection($collection,'system');

        $this->validate();

    }

    public function isSystem(): ?bool
    {
        return $this->system;
    }
    public function getNsOwnerIds(): array
    {
        return $this->ns_owner_ids;
    }

    public function getDestinationSetIds(): array
    {
        return $this->destination_set_ids;
    }

    public function getNumberPerSet(): ?int
    {
        return $this->number_per_set;
    }

    public function getUuid(): array
    {
        return $this->uuids;
    }

    public function getPhaseId(): ?int
    {
        return $this->phase_id;
    }

    public function getParentTypeId(): ?int
    {
        return $this->parent_type_id;
    }
}
