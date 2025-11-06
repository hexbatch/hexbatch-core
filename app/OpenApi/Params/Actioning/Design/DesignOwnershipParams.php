<?php

namespace App\OpenApi\Params\Actioning\Design;


use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchNamespace;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchResource;
use App\Exceptions\HexbatchBadParamException;
use App\Exceptions\HexbatchMissingParamException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\OpenApi\ApiCallBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

/**
 *
 */
#[OA\Schema(schema: 'DesignOwnershipParams')]
class DesignOwnershipParams extends ApiCallBase
{


    protected ?string $type_uuid = null;
    protected ?string $namespace_uuid = null;

    public function __construct(

        #[OA\Property(title: 'The type to transfer', type: HexbatchResource::class,
            example: [new OA\Examples(summary: "type example", value:'birds') ]

        )]
        protected ?string $type = null,

        #[OA\Property(title: 'The new owner', type: HexbatchNamespace::class, nullable: true)]
        protected ?string $namespace = null
    )
    {
        parent::__construct();
    }


    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);

        if ($col->has('type') && $col->get('type')) {
            $this->type =(string)$col->get('type') ;
        } else {
            throw new HexbatchMissingParamException(__('msg.params_missing_type'),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::DESIGN_API_SCHEMA_ISSUE);
        }

        if ($col->has('namespace') && $col->get('namespace')) {
            $this->namespace =(string)$col->get('namespace') ;
        } else {
             throw new HexbatchMissingParamException(__('msg.params_missing_namespace'),
                 \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                 RefCodes::DESIGN_API_SCHEMA_ISSUE);
        }

        if (Utilities::is_uuid($this->namespace)) {
            $this->namespace_uuid = $this->namespace;
        } else {
            $this->namespace_uuid = UserNamespace::resolveNamespace(value: $this->namespace,throw_exception: false)?->ref_uuid;

            if (!$this->namespace_uuid) {
                throw new HexbatchBadParamException(__('msg.params_bad_type'),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::DESIGN_API_SCHEMA_ISSUE);
            }
        }

        if (Utilities::is_uuid($this->type)) {
            $this->type_uuid = $this->type;
        } else {
            $this->type_uuid =
                ElementType::resolveType(value: $this->type,context_namespace_uuid: $this->namespace_uuid,throw_exception: false)?->ref_uuid;
            if (!$this->type_uuid) {
                throw new HexbatchBadParamException(__('msg.params_bad_type'),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::DESIGN_API_SCHEMA_ISSUE);
            }
        }

        if (Utilities::is_uuid($this->namespace)) {
            $this->namespace_uuid = $this->namespace;
        } else {
            $this->namespace_uuid = UserNamespace::getThisNamespace(name: $this->namespace);
        }


    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['type'] = $this->getType();
        $ret['type_uuid'] = $this->type_uuid;
        $ret['namespace'] = $this->getNamespace();
        $ret['namespace_uuid'] = $this->namespace_uuid;
        return $ret;
    }

    public function getTypeUuid(): ?string
    {
        return $this->type_uuid;
    }

    public function getNamespaceUuid(): ?string
    {
        return $this->namespace_uuid;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }






}
