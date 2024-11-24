<?php
namespace App\Api\Cmd\Design;

use App\Api\BaseParams;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Types\TypeOfLifecycle;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\UserNamespace;
use App\Rules\ElementTypeNameReq;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait DesignParams
{
    use BaseParams;
    protected ?int $namespace_id = null;
    protected ?int $server_id = null;

    protected ?string $uuid = null;
    protected ?string $type_name = null;
    protected bool $system = true;
    protected bool $final_type = false;

    protected ?TypeOfLifecycle $lifecycle =null;
    protected ?TypeOfServerAccess $access =null;

    public function getAccess(): ?TypeOfServerAccess
    {
        return $this->access;
    }



    protected function validate() {

        if (!$this->namespace_id) {
            throw new HexbatchNotPossibleException(__('msg.type_must_have_ns'),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_SCHEMA_ISSUE);
        }

        try {
            $namespace = UserNamespace::findOrFail($this->namespace_id);
            if ($this->type_name) {
                Validator::make(['type_name' => $this->type_name], [
                    'type_name' => ['required', 'string', new ElementTypeNameReq(null,$namespace)],
                ])->validate();
            }
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_INVALID_NAME);
        }

        if (!$this->type_name) {
            throw new HexbatchNotPossibleException(__('msg.type_must_have_name'),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_INVALID_NAME);
        }
    }

    public function fromCollection(Collection $collection)
    {
        $this->namespace_id = static::intRefFromCollection($collection,'namespace_id');
        $this->server_id = static::intRefFromCollection($collection,'server_id');
        $this->uuid = static::uuidFromCollection($collection,'uuid');
        $this->type_name = static::stringFromCollection($collection,'type_name');
        $this->system = static::boolFromCollection($collection,'system');
        $this->final_type = static::boolFromCollection($collection,'final_type');
        $this->lifecycle = TypeOfLifecycle::getFromCollection($collection,'lifecycle');
        $this->access = TypeOfServerAccess::getFromCollection($collection,'access');

        $this->validate();
    }



    public function getNamespaceId(): ?int
    {
        return $this->namespace_id;
    }

    public function getServerId(): ?int
    {
        return $this->server_id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getTypeName(): ?string
    {
        return $this->type_name;
    }

    public function isSystem(): bool
    {
        return $this->system;
    }

    public function isFinalType(): bool
    {
        return $this->final_type;
    }

    public function getLifecycle(): ?TypeOfLifecycle
    {
        return $this->lifecycle;
    }



}
