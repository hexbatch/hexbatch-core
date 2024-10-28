<?php

namespace App\Api\Actions\Design\Create;

use App\Api\Actions\AInterfaces\IDataInput;
use App\Api\Actions\AInterfaces\IParamsJson;
use App\Api\Actions\AInterfaces\IParamsSystem;
use App\Api\Actions\AInterfaces\IParamsThing;
use App\Enums\Types\TypeOfLifecycle;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Server;
use App\Models\UserNamespace;
use App\Rules\ElementTypeNameReq;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class DataInput implements IDataInput
{

    protected ?Server $server = null;
    protected ?UserNamespace $namespace = null;
    protected ?string $uuid = null;
    protected ?string $type_name = null;
    protected bool $is_system = false;
    protected bool $is_final_type = false;
    protected ?TypeOfLifecycle $lifecycle = null;


    public static function createFromParamsJson(IParamsJson $params): IDataInput
    {
        return new static;
    }

    public static function createFromParamsThing(IParamsThing $params): IDataInput
    {
        return new static;
    }

    public static function createFromParamsSystem(IParamsSystem $params): IDataInput
    {
        /** @var ParamsSystem $params */

        $node = new static;
        $node->namespace = $params->getNamespace();
        $node->uuid = $params->getUuid();
        $node->type_name = $params->getTypeName();
        $node->is_system = $params->isSystem();
        $node->is_final_type = $params->isFinalType();
        $node->lifecycle = $params->getLifecycle()??TypeOfLifecycle::DEVELOPING;

        $node->doValidation();
        return $node;
    }

    public function doValidation(): void
    {


        try {
            Validator::make(['type_name' => $this->type_name], [
                'type_name' => ['required', 'string', new ElementTypeNameReq(null,$this->getNamespace())],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_INVALID_NAME);
        }
    }

    public function getServer(): ?Server
    {
        return $this->server;
    }

    public function getNamespace(): ?UserNamespace
    {
        return $this->namespace;
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
        return $this->is_system;
    }

    public function isFinalType(): bool
    {
        return $this->is_final_type;
    }

    public function getLifecycle(): ?TypeOfLifecycle
    {
        return $this->lifecycle;
    }


}
