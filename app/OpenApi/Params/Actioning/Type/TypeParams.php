<?php

namespace App\OpenApi\Params\Actioning\Type;


use App\Models\ElementType;
use App\OpenApi\ApiCallBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

/**
 *
 */
#[OA\Schema(schema: 'TypeParams')]
class TypeParams extends ApiCallBase
{

    #[OA\Property(title: 'Type',description: 'The type. Can be uuid or name')]
    protected ?string $type_uuid = null;
    public function __construct(
        protected ?ElementType $given_type = null
    )
    {
        parent::__construct();
       $this->type_uuid = $this->given_type->ref_uuid;
    }


    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);

        if (!$this->given_type && $col->has('type_uuid') && $col->get('type_uuid')) {
            $this->type_uuid =(string)$col->get('type_uuid') ;
        }
    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['type_uuid'] = $this->given_type->ref_uuid;
        return $ret;
    }

    public function getTypeUuid(): ?string
    {
        return $this->type_uuid;
    }

    public function getGivenType(): ?ElementType
    {
        return $this->given_type;
    }







}
