<?php

namespace App\OpenApi\Params\Listing\Design;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\LocationBound;
use App\Models\UserNamespace;
use App\Rules\NamespaceMemberReq;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'ListAttributeParams')]
class ListAttributeParams extends ShowAttributeParams
{

    #[OA\Property(title: 'Namespace',description: 'The attribute is in this namespace. Can be uuid or name')]
    protected ?string $namespace_ref = null;


    #[OA\Property(title: 'Shape',description: 'The attribute is using this shape. Can be uuid or name')]
    protected ?string $shape_ref = null;


    public function __construct(
        protected ?UserNamespace     $given_namespace = null,
        protected ?LocationBound     $given_location = null,
    )
    {
        parent::__construct();
        $this->namespace_ref = $this->given_namespace?->ref_uuid;
        $this->shape_ref = $this->given_location?->ref_uuid;
    }

    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col,$do_validation);

        if (!$this->given_namespace) {
            $this->namespace_ref = static::stringFromCollection(collection: $col,param_name: 'namespace_ref');
            $this->given_namespace = UserNamespace::resolveNamespace(value: $col->get('namespace_ref'));
            $this->namespace_ref = $this->given_namespace->ref_uuid;
        }

        if (!$this->given_location) {
            $this->shape_ref = static::stringFromCollection(collection: $col,param_name: 'shape_ref');
            $this->given_location = LocationBound::resolveLocation(value: $this->shape_ref);
            $this->shape_ref = $this->given_location->ref_uuid;
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
        $what['shape_ref'] = $this->shape_ref;
        return $what;
    }



    public function getGivenNamespace(): ?UserNamespace
    {
        return $this->given_namespace;
    }

    public function getGivenLocation(): ?LocationBound
    {
        return $this->given_location;
    }




}
