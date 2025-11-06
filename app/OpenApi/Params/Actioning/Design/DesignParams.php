<?php

namespace App\OpenApi\Params\Actioning\Design;


use App\Data\ApiParams\OpenApi\Common\HexbatchResourceName;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchResource;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Helpers\Utilities;
use App\Models\ElementType;
use App\Models\TimeBound;
use App\Models\UserNamespace;
use App\OpenApi\ApiCallBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

/**
 *
 */
#[OA\Schema(schema: 'DesignParams')]
class DesignParams extends ApiCallBase
{
    #[OA\Property( title: 'Type name', type: HexbatchResourceName::class,
        example: [new OA\Examples(summary: "name example", value:'he312345') ]

    )]
    protected ?string $type_name = null;

    #[OA\Property(title: 'Schedule name or uuid', type:  HexbatchResource::class, nullable: true)]
    protected ?string $schedule = null;


    #[OA\Property(title: 'Is this a final type?', default: false)]
    protected bool $is_final = false;

    #[OA\Property(title: 'Is this a final type?', default: TypeOfServerAccess::IS_PUBLIC)]
    protected TypeOfServerAccess $access = TypeOfServerAccess::IS_PUBLIC;





    public function __construct(
        protected ?string $bound_uuid = null,
        protected ?string $namespace_uuid = null,
        protected ?ElementType $edit_type = null,
        protected ? UserNamespace $namespace = null
    )
    {
        parent::__construct();
        $this->namespace_uuid = $this->namespace?->getUuid();

    }


    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);

        if ($do_validation &&$col->has('type_name') && $col->get('type_name')) {
            $this->type_name = (string)$col->get('type_name');
            ElementType::validateTypeName(name: $this->type_name,namespace: $this->namespace,me: $this->edit_type);
        }

        if ($col->has('schedule') && $col->get('schedule')) {
            $this->schedule =(string)$col->get('schedule') ;
            $this->bound_uuid = TimeBound::resolveSchedule(value: $this->schedule)->ref_uuid;
        }

        if ($col->has('is_final')) {
            $this->is_final = Utilities::boolishToBool($col->get('is_final'));
        }

        if ($col->has('access') && $col->get('access')) {
            $this->access = TypeOfServerAccess::tryFromInput($col->get('access'));
        }

    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['type_name'] = $this->type_name;
        $ret['schedule'] = $this->schedule;
        $ret['access'] = $this->access;
        $ret['is_final'] = $this->is_final;

        $ret['namespace_uuid'] = $this->namespace_uuid;
        $ret['bound_uuid'] = $this->bound_uuid;
        $ret['edit_uuid'] = $this->edit_type?->ref_uuid;
        return $ret;
    }


    public function getTypeName(): ?string
    {
        return $this->type_name;
    }

    public function getScheduleUuid(): ?string
    {
        return $this->bound_uuid;
    }

    public function getNamespaceUuid(): ?string
    {
        return $this->namespace_uuid;
    }

    public function getNamespace(): UserNamespace
    {
        if (!$this->namespace && $this->getNamespaceUuid()) {
            $this->namespace = UserNamespace::getThisNamespace(uuid: $this->getNamespaceUuid());
        }
        return $this->namespace;
    }

    public function isFinal(): bool
    {
        return $this->is_final;
    }

    public function getAccess(): TypeOfServerAccess
    {
        return $this->access;
    }






}
