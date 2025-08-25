<?php

namespace App\OpenApi\Params\Design;

use App\Models\Attribute;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Api\ApiParamBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

/**
 * Type Design
 */
#[OA\Schema(schema: 'DesignAttributeDestroyParams')]
class DesignAttributeDestroyParams extends ApiParamBase
{
    #[OA\Property(title: 'Attribute',description: 'Attribute can be the full name or uuid')]
    protected ?string $attribute_reference = null;

    public function __construct(
        protected ?Attribute       $given_attribute = null,
        protected ?UserNamespace $namespace = null,

    )
    {

    }

    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);


        if (!$this->given_attribute) {
            if ($col->has('attribute_reference') && $col->get('attribute_reference')) {
                $this->given_attribute = Attribute::resolveAttribute(value: $col->get('attribute_reference'));
                $this->attribute_reference = $col->get('attribute_reference');
            }
        }
    }

    public function getGivenAttribute(): ?Attribute
    {
        return $this->given_attribute;
    }


}
