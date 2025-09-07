<?php

namespace App\OpenApi\Params\Listing\Set;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\UserNamespace;
use App\Rules\NamespaceMemberReq;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'ListSetParams')]
class ListSetParams extends ShowSetParams
{

    #[OA\Property(title: 'Namespace',description: 'The set is in this namespace. Can be uuid or name')]
    protected ?string $namespace_ref = null;


    #[OA\Property(title: 'Type',description: 'The set is defined by this type. Can be uuid or name')]
    protected ?string $type_ref = null;


    #[OA\Property(title: 'Parent set',description: 'The element is in this set')]
    protected ?string $parent_set_ref = null;




    public function __construct(
        protected ?UserNamespace $given_namespace = null,
        protected ?ElementType   $given_type = null,
        protected ?ElementSet    $given_parent_set = null,
        protected ?Phase    $working_phase = null,
    )
    {
        parent::__construct();
        $this->namespace_ref = $this->given_namespace?->ref_uuid;
        $this->type_ref = $this->given_type?->ref_uuid;
        $this->parent_set_ref = $this->given_parent_set?->ref_uuid;
    }

    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col,$do_validation);

        if (!$this->given_namespace) {
            $this->namespace_ref = static::stringFromCollection(collection: $col,param_name: 'namespace_ref');
            $this->given_namespace = UserNamespace::resolveNamespace(value: $this->namespace_ref);
            $this->namespace_ref = $this->given_namespace?->ref_uuid;
        }


        if (!$this->given_type) {
            $this->type_ref = static::stringFromCollection(collection: $col,param_name: 'type_ref');
            $this->given_type = ElementType::resolveType(value: $this->type_ref);
            $this->type_ref = $this->given_type?->ref_uuid;
        }

        if (!$this->given_parent_set) {
            $this->parent_set_ref = static::stringFromCollection(collection: $col,param_name: 'parent_set_ref');
            $this->given_parent_set = ElementType::resolveType(value: $this->parent_set_ref);
            $this->parent_set_ref = $this->given_parent_set?->ref_uuid;
        }



        if ($do_validation) {
            try {
                Validator::make(
                    [
                        'namespace_ref' => $this->given_namespace
                    ],
                    [
                        'namespace_ref' => ['nullable', new NamespaceMemberReq]
                    ]
                )->validate();

            } catch (ValidationException $v) {
                throw new HexbatchNotPossibleException($v->getMessage(),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::BAD_REGISTRATION);
            }
        }


    }

    public  function toArray() : array  {
        $what = parent::toArray();
        $what['namespace_ref'] = $this->namespace_ref;
        $what['type_ref'] = $this->type_ref;
        $what['parent_set_ref'] = $this->parent_set_ref;
        return $what;
    }



    public function getGivenNamespace(): ?UserNamespace
    {
        return $this->given_namespace;
    }



    public function getGivenType(): ?ElementType
    {
        return $this->given_type;
    }

    public function getGivenParentSet(): ?ElementSet
    {
        return $this->given_parent_set;
    }

    public function getWorkingPhase(): ?Phase
    {
        return $this->working_phase;
    }








}
