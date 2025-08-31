<?php

namespace App\OpenApi\Params\Listing\Design;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\UserNamespace;
use App\OpenApi\Params\Listing\ListDataBaseParams;
use App\Rules\NamespaceMemberReq;
use App\Rules\TimeInputReq;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'ListScheduleParams')]
class ListScheduleParams extends ListDataBaseParams
{

    #[OA\Property(title: 'Namespace',description: 'The schedule is in this namespace. Can be uuid or name. If missing will use calling namespace')]
    protected ?string $namespace_ref = null;


    #[OA\Property( title: 'Scheduled before',description: "Iso 8601 datetime, to see if the schedule is before this", format: 'datetime',example: "2025-02-25T15:00:59-06:00",nullable: true)]
    protected ?string $before = null;

    #[OA\Property( title: 'Scheduled before',description: "Iso 8601 datetime, to see if the schedule is after this", format: 'datetime',example: "2025-02-25T15:00:59-06:00",nullable: true)]
    protected ?string $after = null;

    #[OA\Property( title: 'Scheduled during',description: "Iso 8601 datetime, to see if the schedule includes this", format: 'datetime',example: "2025-02-25T15:00:59-06:00",nullable: true)]
    protected ?string $during = null;



    public function __construct(
        protected ?UserNamespace     $given_namespace = null
    )
    {
        $this->namespace_ref = $this->given_namespace?->ref_uuid;
    }

    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col,$do_validation);

        if (!$this->given_namespace) {
            if ($col->has('namespace_ref') && $col->get('namespace_ref')) {
                $this->given_namespace = UserNamespace::resolveNamespace(value: $col->get('namespace_ref'));
                $this->namespace_ref = $this->given_namespace->ref_uuid;
            }
        }


        $this->before = static::stringFromCollection(collection: $col,param_name: 'before');
        $this->after = static::stringFromCollection(collection: $col,param_name: 'after');
        $this->during = static::stringFromCollection(collection: $col,param_name: 'during');

        if ($do_validation) {
            try {
                Validator::make(
                    [
                        'namespace_ref' => $this->given_namespace,
                        'before' => $this->before,
                        'after' => $this->after,
                        'during' => $this->during
                    ],
                    [
                        'namespace_ref' => ['nullable', new NamespaceMemberReq],
                        'before' => ['nullable', new TimeInputReq],
                        'after' => ['nullable', new TimeInputReq],
                        'during' => ['nullable', new TimeInputReq],
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
        $what['before'] = $this->before;
        $what['after'] = $this->after;
        $what['during'] = $this->during;
        return $what;
    }

    public function getNamespaceRef(): ?string
    {
        return $this->namespace_ref;
    }

    public function getBefore(): ?string
    {
        return $this->before;
    }

    public function getAfter(): ?string
    {
        return $this->after;
    }

    public function getDuring(): ?string
    {
        return $this->during;
    }

    public function getGivenNamespace(): ?UserNamespace
    {
        return $this->given_namespace;
    }




}
