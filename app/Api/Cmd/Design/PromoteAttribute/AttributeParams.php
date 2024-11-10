<?php
namespace App\Api\Cmd\Design\PromoteAttribute;

use App\Api\Cmd\BaseParams;
use App\Enums\Types\TypeOfApproval;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Rules\AttributeNameReq;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


trait AttributeParams
{
    use BaseParams;

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

    protected function validate() {

        if (!$this->owner_element_type_id) {
            throw new HexbatchNotPossibleException(__('msg.attribute_schema_must_have_type'),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);

        }

        try {
            Validator::make(['attribute_name' => $this->attribute_name], [
                'attribute_name' => ['required', 'string', new AttributeNameReq($this->owner_element_type_id,null)],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }
    }

    public function fromCollection(Collection $collection)
    {
        $this->owner_element_type_id = static::intRefFromCollection($collection,'owner_element_type_id');
        $this->parent_attribute_id = static::intRefFromCollection($collection,'parent_attribute_id');
        $this->design_attribute_id = static::intRefFromCollection($collection,'design_attribute_id');

        $this->uuid = static::uuidFromCollection($collection,'uuid');
        $this->attribute_approval = TypeOfApproval::getFromCollection($collection,'attribute_approval');
        $this->system = static::boolFromCollection($collection,'system');
        $this->final = static::boolFromCollection($collection,'final');
        $this->abstract = static::boolFromCollection($collection,'abstract');
        $this->seen_by_child = static::boolFromCollection($collection,'seen_by_child');
        $this->attribute_name = static::stringFromCollection($collection,'attribute_name');

        $this->validate();
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
