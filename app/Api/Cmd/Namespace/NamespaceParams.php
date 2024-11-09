<?php
namespace App\Api\Cmd\Namespace;

use App\Api\Cmd\BaseParams;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Rules\NamespaceNameReq;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait NamespaceParams
{
    use BaseParams;

    protected ?int $namespace_user_id = null;
    protected ?int $namespace_server_id = null;
    protected ?int $namespace_type_id = null;
    protected ?int $public_element_id = null;
    protected ?int $private_element_id = null;
    protected ?int $namespace_home_set_id = null;
    protected ?string $namespace_public_key = null;
    protected ?string $namespace_name = null;
    protected ?string $uuid = null;
    protected bool $system = false;


    protected function validate() {
        try {
            Validator::make(['namespace_name' => $this->namespace_name], [
                'namespace_name' => ['required', 'string', new NamespaceNameReq()],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::NAMESPACE_SCHEMA_ISSUE);
        }
    }

    public function fromCollection(Collection $collection)
    {
        $this->namespace_user_id = static::intRefFromCollection($collection,'namespace_user_id');
        $this->namespace_server_id = static::intRefFromCollection($collection,'namespace_server_id');
        $this->namespace_type_id = static::intRefFromCollection($collection,'namespace_type_id');
        $this->private_element_id = static::intRefFromCollection($collection,'private_element_id');
        $this->public_element_id = static::intRefFromCollection($collection,'public_element_id');
        $this->namespace_home_set_id = static::intRefFromCollection($collection,'uuid');
        $this->namespace_home_set_id = static::intRefFromCollection($collection,'namespace_home_set_id');
        $this->uuid = static::uuidFromCollection($collection,'uuid');
        $this->namespace_public_key = static::stringFromCollection($collection,'namespace_public_key');
        $this->namespace_name = static::stringFromCollection($collection,'namespace_name');

        $this->validate();
    }


    public function getNamespaceUserId(): ?int
    {
        return $this->namespace_user_id;
    }

    public function getNamespaceServerId(): ?int
    {
        return $this->namespace_server_id;
    }

    public function getNamespaceTypeId(): ?int
    {
        return $this->namespace_type_id;
    }

    public function getPublicElementId(): ?int
    {
        return $this->public_element_id;
    }

    public function getPrivateElementId(): ?int
    {
        return $this->private_element_id;
    }

    public function getNamespaceHomeSetId(): ?int
    {
        return $this->namespace_home_set_id;
    }

    public function getNamespacePublicKey(): ?string
    {
        return $this->namespace_public_key;
    }

    public function getNamespaceName(): ?string
    {
        return $this->namespace_name;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function isSystem(): bool
    {
        return $this->system;
    }





}
