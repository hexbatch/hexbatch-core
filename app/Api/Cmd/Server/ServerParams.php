<?php
namespace App\Api\Cmd\Server;

use App\Api\BaseParams;
use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Rules\ServerNameReq;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ServerParams
{
    use BaseParams;

    protected ?int $server_id = null;
    protected ?int $owning_namespace_id = null;
    protected ?int $server_type_id = null;
    protected ?string $uuid = null;
    protected ?TypeOfServerStatus $server_status = null;
    protected ?int $access_token_expires_at = null;
    protected ?string $server_name = null;
    protected ?string $server_domain = null;
    protected ?string $server_url = null;
    protected ?string $server_access_token = null;

    protected ?bool $system = null;

    protected function validate() {
        try {
            Validator::make(['server_name' => $this->server_name], [
                'server_name' => ['required', 'string', new ServerNameReq()],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::SERVER_SCHEMA_ISSUE);
        }
    }

    public function fromCollection(Collection $collection)
    {
        $this->server_id = static::intRefFromCollection($collection,'server_id');
        $this->owning_namespace_id = static::intRefFromCollection($collection,'owning_namespace_id');
        $this->server_type_id = static::intRefFromCollection($collection,'server_type_id');
        $this->uuid = static::uuidFromCollection($collection,'uuid');
        $this->server_status = TypeOfServerStatus::getFromCollection($collection,'server_status');
        $this->server_domain = static::stringFromCollection($collection,'server_domain');
        $this->server_url = static::stringFromCollection($collection,'server_url');
        $this->system = static::boolFromCollection($collection,'system');
        $this->server_name = static::stringFromCollection($collection,'server_name');
        $this->server_access_token = static::stringFromCollection($collection,'server_access_token');
        $this->access_token_expires_at = static::unixTsFromCollection($collection,'access_token_expires_at');

        $this->validate();
    }

    public function getServerUrl(): ?string
    {
        return $this->server_url;
    }

    public function getServerId(): ?int
    {
        return $this->server_id;
    }

    public function getOwningNamespaceId(): ?int
    {
        return $this->owning_namespace_id;
    }

    public function isSystem(): ?bool
    {
        return $this->system;
    }

    public function getServerTypeId(): ?int
    {
        return $this->server_type_id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getServerStatus(): ?TypeOfServerStatus
    {
        return $this->server_status;
    }

    public function getAccessTokenExpiresAt(): ?int
    {
        return $this->access_token_expires_at;
    }

    public function getServerName(): ?string
    {
        return $this->server_name;
    }

    public function getServerDomain(): ?string
    {
        return $this->server_domain;
    }

    public function getServerAccessToken(): ?string
    {
        return $this->server_access_token;
    }






}
