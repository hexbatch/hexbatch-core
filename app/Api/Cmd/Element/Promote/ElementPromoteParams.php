<?php
namespace App\Api\Cmd\Element\Promote;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Phase;
use App\Models\Thing;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\ElementPromote;
use Illuminate\Support\Collection;

class ElementPromoteParams extends ElementPromote implements IActionParams,IActionOaInput
{

    protected array $ns_owner_ids = [];
    protected array $destination_set_ids = [];
    protected ?int $number_per_set = null;
    protected array $uuids = [];

    protected ?int $phase_id = null;
    protected ?int $parent_type_id = null;

    const NO_SETS_MADE_YET_STUB_ID = -1;

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

    public function fromThing(Thing $thing): void
    {
        // todo pull the data from the thing and fill in the data here from the json stored there
    }

    public function fromCollection(Collection $collection)
    {
        $this->ns_owner_ids = $meep = $collection->get('ns_owner_ids',[]);
        if (!is_array($meep)) { $this->ns_owner_ids = [$this->ns_owner_ids];}
        array_filter($this->ns_owner_ids, fn($value) => !empty(trim($value)) );


        $this->destination_set_ids = $meep = $collection->get('destination_set_ids',[]);
        if (!is_array($meep)) { $this->destination_set_ids = [$this->destination_set_ids];}
        if (empty($this->destination_set_ids)) {
            $this->destination_set_ids = UserNamespace::whereIn('id',$this->ns_owner_ids)->pluck('namespace_home_set_id')->toArray();
        }
        array_filter($this->destination_set_ids, fn($value) => !empty(trim($value)) );



        $this->uuids = $meep = $collection->get('uuids',[]);
        if (!is_array($meep)) { $this->uuids = [$this->uuids];}
        array_filter($this->uuids, fn($value) => !empty(trim($value)) );


        $this->number_per_set = (int)$collection->get('number_per_set',1);
        if ($this->number_per_set < 1) { $this->number_per_set = 1; }

        $this->phase_id = (int)$collection->get('phase_id');
        if (!$this->phase_id)  {$this->phase_id = Phase::getDefaultPhase()?->id;}

        $this->parent_type_id = (int)$collection->get('parent_type_id');
        if (!$this->parent_type_id)  {$this->parent_type_id = null;}

        $this->validate();
    }

    public function toArray() : array {
        return [];
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
