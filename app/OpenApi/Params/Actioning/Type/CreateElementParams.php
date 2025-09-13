<?php

namespace App\OpenApi\Params\Actioning\Type;



use App\Models\ElementType;
use App\Models\Phase;
use App\Models\UserNamespace;
use App\OpenApi\ApiThingBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

/*
  given_type_uuid: uuid of the type
  given_namespace_uuid: uuid of the namespace to put the element into, if not given, the same namespace as the call will be used
  given_phase_uuid: uuid of the phase, if not given, the default will be used
  number_to_create: if missing will be one

 */
#[OA\Schema(schema: 'CreateElementParams')]
class CreateElementParams extends ApiThingBase
{

    #[OA\Property(title: 'Type',description: 'The element is made from this type. Can be uuid or name')]
    protected ?string $type_ref = null;

    #[OA\Property(title: 'Namespace',description: 'The new elements are put into this namespace. Can be uuid or name. If missing will be put into calling namespace')]
    protected ?string $namespace_ref = null;


    #[OA\Property(title: 'Phase',description: 'The new elements are put into this phase. Can be uuid or name. If missing will be put into the default phase')]
    protected ?string $phase_ref = null;


    #[OA\Property(title: 'Number to create',description: 'If missing will be one.')]
    protected int $number_to_create = 1;


    public function __construct(
        protected ?ElementType       $given_type = null,
        protected ?Phase             $given_phase = null,
        protected ?UserNamespace     $given_namespace = null,
        protected ?UserNamespace     $calling_namespace = null,

    )
    {
        parent::__construct();
        $this->type_ref = $this->given_type?->ref_uuid;
        $this->phase_ref = $this->given_phase?->ref_uuid;
        $this->namespace_ref = $this->given_namespace?->ref_uuid;
        if (!$this->namespace_ref && $this->calling_namespace) {
            $this->namespace_ref = $this->calling_namespace->ref_uuid;
        }


    }


    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);


        if (!$this->given_type) {
            if ($col->has('type_ref') && $col->get('type_ref')) {
                $this->given_type = ElementType::resolveType(value: $col->get('type_ref'));
                $this->type_ref = $this->given_type->ref_uuid;
            }
        }

        if (!$this->given_phase) {
            if ($col->has('phase_ref') && $col->get('phase_ref')) {
                $this->given_phase = Phase::resolvePhase(value: $col->get('phase_ref'));
                $this->phase_ref = $this->given_phase->ref_uuid;
            }
        }

        if (!($this->calling_namespace)) {
            $this->namespace_ref = static::stringFromCollection(collection: $col,param_name: 'namespace_ref');
            $this->given_namespace = UserNamespace::resolveNamespace(value: $this->namespace_ref);
            $this->namespace_ref = $this->given_namespace?->ref_uuid;
        }


        if ($col->has('number_to_create') && (intval($col->get('number_to_create')) > 1) ) {
            $this->number_to_create = intval($col->get('number_to_create'));
        }

    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['type_ref'] = $this->type_ref;
        $ret['phase_ref'] = $this->phase_ref;
        $ret['namespace_ref'] = $this->namespace_ref;
        $ret['number_to_create'] = $this->number_to_create;

        return $ret;
    }

    public function getTypeRef(): ?string
    {
        return $this->type_ref;
    }

    public function getNamespaceRef(): ?string
    {
        return $this->namespace_ref;
    }

    public function getPhaseRef(): ?string
    {
        return $this->phase_ref;
    }

    public function getNumberToCreate(): int
    {
        return $this->number_to_create;
    }




}
