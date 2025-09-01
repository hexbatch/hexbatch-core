<?php

namespace App\OpenApi\Params\Actioning\Design;


use App\Exceptions\HexbatchBadParamException;
use App\Exceptions\RefCodes;
use App\Models\ElementType;
use App\OpenApi\ApiDataBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

/**
 *
 */
#[OA\Schema(schema: 'DesignParentParams')]
class DesignParentParams extends ApiDataBase
{

    protected ?string $type_uuid = null;


    /** @var string[] $parent_uuids */
    protected array $parent_uuids = [];


    public function __construct(
        protected ?ElementType $given_type = null,

        #[OA\Property( title:"Parents",items:  new OA\Items(ref: '#/components/schemas/HexbatchResource'),nullable: true)]
        protected array $parents = []
    )
    {
       $this->type_uuid = $this->given_type?->ref_uuid;
    }


    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);

        if (!$this->given_type && $col->has('type_uuid') && $col->get('type_uuid')) {
            $this->type_uuid =(string)$col->get('type_uuid') ;
        }

        if ($col->has('parents') && $col->get('parents')) {
            $raw_parents = $col->get('parents') ;
            if (!is_array($raw_parents)) {
                $raw_parents = [$raw_parents];
            }

            foreach ($raw_parents as $aw) {
                $string_awe = (string)$aw;
                $parent = ElementType::resolveType(value: $string_awe,throw_exception: false);
                if (!$parent) {
                    throw new HexbatchBadParamException(__('msg.params_bad_type'),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::DESIGN_API_SCHEMA_ISSUE);
                }
                $this->parent_uuids[] = $parent->ref_uuid;
            }
        }
    }

    public function toArray(): array
    {
        $ret = parent::toArray();

        $ret['type_uuid'] = $this->given_type->ref_uuid;
        $ret['parent_uuids'] = $this->parent_uuids;
        return $ret;
    }

    public function getTypeUuid(): ?string
    {
        return $this->type_uuid;
    }

    public function getParentUuids(): array
    {
        return $this->parent_uuids;
    }

    public function getGivenType(): ?ElementType
    {
        return $this->given_type;
    }







}
