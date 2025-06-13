<?php

namespace App\OpenApi\Params\Design;


use App\Models\ElementType;
use App\Sys\Res\Types\Stk\Root\Api\ApiParamBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

/**
 * Type Design
 */
#[OA\Schema(schema: 'DesignDestroyParams')]
class DesignDestroyParams extends ApiParamBase
{

    protected ?string $type_uuid = null;
    public function __construct(
        protected ?ElementType    $destroy_type = null
    )
    {
       $this->type_uuid = $this->destroy_type->ref_uuid;
    }


    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);

        if (!$this->destroy_type && $col->has('type_uuid') && $col->get('type_uuid')) {
            $this->type_uuid =(string)$col->get('type_uuid') ;
        }
    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['type_uuid'] = $this->destroy_type->ref_uuid;
        return $ret;
    }

    public function getTypeUuid(): ?string
    {
        return $this->type_uuid;
    }

    public function getDestroyType(): ?ElementType
    {
        return $this->destroy_type;
    }







}
