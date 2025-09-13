<?php

namespace App\OpenApi\Params\Listing\Design;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\UserNamespace;
use App\OpenApi\Params\Listing\ListThingBaseParams;
use App\Rules\NamespaceMemberReq;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'ListLocationParams')]
class ListLocationParams extends ListThingBaseParams
{

    #[OA\Property(title: 'Namespace',description: 'The location is in this namespace. Can be uuid or name')]
    protected ?string $namespace_ref = null;


    public function __construct(
        protected ?UserNamespace     $given_namespace = null
    )
    {
        parent::__construct();
        $this->namespace_ref = $this->given_namespace?->ref_uuid;
    }

    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col,$do_validation);

        if (!$this->given_namespace) {
            $this->namespace_ref = static::stringFromCollection(collection: $col,param_name: 'namespace_ref');
            $this->given_namespace = UserNamespace::resolveNamespace(value: $this->namespace_ref);
            $this->namespace_ref = $this->given_namespace?->ref_uuid;
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
        return $what;
    }

    public function getNamespaceRef(): ?string
    {
        return $this->namespace_ref;
    }


    public function getGivenNamespace(): ?UserNamespace
    {
        return $this->given_namespace;
    }




}
