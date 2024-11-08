<?php
namespace App\Api\Cmd\Design\PromoteAttribute;

use App\Enums\Types\TypeOfApproval;
use App\Models\Attribute;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignAttributePromote;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignPromote;
use Illuminate\Support\Collection;

class APSetupForSystem
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

    public function makeCollection() : Collection {
        return new Collection([
            'owner_element_type_id' => $this->owner_element_type_id,
            'parent_attribute_id' => $this->parent_attribute_id,
            'design_attribute_id' => $this->design_attribute_id,
            'uuid' => $this->uuid,
            'type_name' => $this->attribute_name,
            'system' => $this->system,
            'final' => $this->final,
            'abstract' => $this->abstract,
            'seen_by_child' => $this->seen_by_child,
            'attribute_approval' => $this->attribute_approval->value,
        ]);
    }

    public function setOwnerElementTypeId(?int $owner_element_type_id): APSetupForSystem
    {
        $this->owner_element_type_id = $owner_element_type_id;
        return $this;
    }

    public function setParentAttributeId(?int $parent_attribute_id): APSetupForSystem
    {
        $this->parent_attribute_id = $parent_attribute_id;
        return $this;
    }

    public function setDesignAttributeId(?int $design_attribute_id): APSetupForSystem
    {
        $this->design_attribute_id = $design_attribute_id;
        return $this;
    }

    public function setUuid(?string $uuid): APSetupForSystem
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setAttributeName(?string $attribute_name): APSetupForSystem
    {
        $this->attribute_name = $attribute_name;
        return $this;
    }

    public function setSystem(bool $system): APSetupForSystem
    {
        $this->system = $system;
        return $this;
    }

    public function setFinal(bool $final): APSetupForSystem
    {
        $this->final = $final;
        return $this;
    }

    public function setAbstract(bool $abstract): APSetupForSystem
    {
        $this->abstract = $abstract;
        return $this;
    }

    public function setSeenByChild(bool $seen_by_child): APSetupForSystem
    {
        $this->seen_by_child = $seen_by_child;
        return $this;
    }

    public function setAttributeApproval(TypeOfApproval $attribute_approval): APSetupForSystem
    {
        $this->attribute_approval = $attribute_approval;
        return $this;
    }


    public function doParamsAndResponse() :Attribute {
        /**
         * @var AttributePromoteParams $promo_params
         */
        $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,DesignAttributePromote::getClassUuid());
        $promo_params->fromCollection($this->makeCollection());

        /**
         * @type AttributePromoteResponse $promo_work
         */
        $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,DesignPromote::getClassUuid());

        $promo_results = $promo_work::doWork($promo_params);
        return $promo_results->getGeneratedAttribute();
    }

}
