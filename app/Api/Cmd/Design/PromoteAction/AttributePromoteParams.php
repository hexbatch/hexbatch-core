<?php
namespace App\Api\Cmd\Design\PromoteAction;

use App\Api\Cmd\IActionOaInput;
use App\Api\Cmd\IActionParams;

use App\Enums\Types\TypeOfApproval;

use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignAttributePromotion;
use Illuminate\Support\Collection;

class AttributePromoteParams extends DesignAttributePromotion implements IActionParams,IActionOaInput
{

    protected ?int $owner_element_type_id = null;
    protected ?int $parent_attribute_id = null;
    protected ?int $design_attribute_id = null;

    protected ?string $uuid = null;
    protected ?string $attribute_name = null;

    protected bool $system = true;
    protected bool $final = false;
    protected bool $abstract = false;
    protected bool $seen_by_child = false;

    protected TypeOfApproval $attribute_approval = TypeOfApproval::PUBLISHING_APPROVED;



    public function fromThing(Thing $thing): void
    {
        // todo pull the data from the thing and fill in the data here from the json stored there
    }

    public function fromCollection(Collection $collection)
    {
        $this->owner_element_type_id = (int)$collection->get('owner_element_type_id');
        if (!$this->owner_element_type_id)  {$this->owner_element_type_id = null;}

        $this->parent_attribute_id = (int)$collection->get('parent_attribute_id');
        if (!$this->parent_attribute_id)  {$this->parent_attribute_id = null;}

        $this->design_attribute_id = (int)$collection->get('design_attribute_id');
        if (!$this->design_attribute_id)  {$this->design_attribute_id = null;}

        $this->uuid = (string)$collection->get('uuid');
        if (empty($this->uuid)) {$this->uuid = null;}

        $this->attribute_name = (string)$collection->get('attribute_name');

        if ($collection->has('system')) {
            $this->system = (bool)$collection->get('system');
        }

        if ($collection->has('final')) {
            $this->final = (bool)$collection->get('final');
        }

        if ($collection->has('abstract')) {
            $this->abstract = (bool)$collection->get('abstract');
        }

        if ($collection->has('seen_by_child')) {
            $this->seen_by_child = (bool)$collection->get('seen_by_child');
        }

        if ($collection->has('attribute_approval')) {
            $this->attribute_approval = TypeOfApproval::tryFromInput($collection->get('attribute_approval'));
        }

    }

    public function toArray() : array {
        return [];
    }


    public function getOwnerElementTypeId(): ?int
    {
        return $this->owner_element_type_id;
    }

    public function getParentAttributeId(): ?int
    {
        return $this->parent_attribute_id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getAttributeName(): ?string
    {
        return $this->attribute_name;
    }

    public function isSystem(): bool
    {
        return $this->system;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }

    public function getDesignAttributeId(): ?int
    {
        return $this->design_attribute_id;
    }

    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    public function isSeenByChild(): bool
    {
        return $this->seen_by_child;
    }

    public function getAttributeApproval(): TypeOfApproval
    {
        return $this->attribute_approval;
    }





}
