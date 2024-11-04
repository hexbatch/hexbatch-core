<?php
namespace App\Api\Cmd\Namespace\Promote;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Thing;
use App\Rules\NamespaceNameReq;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait NamespaceParams
{


    protected ?int $namespace_user_id = null;
    protected ?int $namespace_server_id = null;
    protected ?int $namespace_type_id = null;
    protected ?int $public_element_id = null;
    protected ?int $private_element_id = null;
    protected ?int $namespace_home_set_id = null;
    protected ?string $namespace_public_key = null;
    protected ?string $namespace_name = null;
    protected ?string $uuid = null;


    public function fromThing(Thing $thing): void
    {
        // todo pull the data from the thing and fill in the data here from the json stored there
    }

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
        $this->namespace_user_id = (int)$collection->get('namespace_user_id');
        if (!$this->namespace_user_id)  {$this->namespace_user_id = null;}

        $this->namespace_server_id = (int)$collection->get('namespace_server_id');
        if (!$this->namespace_server_id)  {$this->namespace_server_id = null;}

        $this->namespace_type_id = (int)$collection->get('namespace_type_id');
        if (!$this->namespace_type_id)  {$this->namespace_type_id = null;}

        $this->private_element_id = (int)$collection->get('private_element_id');
        if (!$this->private_element_id)  {$this->private_element_id = null;}

        $this->public_element_id = (int)$collection->get('public_element_id');
        if (!$this->public_element_id)  {$this->public_element_id = null;}

        $this->namespace_home_set_id = (int)$collection->get('namespace_home_set_id');
        if (!$this->namespace_home_set_id)  {$this->namespace_home_set_id = null;}

        $this->uuid = (string)$collection->get('uuid');
        if (empty($this->uuid)) {$this->uuid = null;}

        $this->namespace_public_key = (string)$collection->get('namespace_public_key');
        if (empty($this->namespace_public_key)) {$this->namespace_public_key = null;}

        $this->namespace_name = (string)$collection->get('namespace_name');
        if (empty($this->namespace_name)) {$this->namespace_name = null;}

        $this->validate();
    }

    public function toArray() : array {
        return [];
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





}
