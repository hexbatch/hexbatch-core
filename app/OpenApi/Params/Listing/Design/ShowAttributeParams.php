<?php

namespace App\OpenApi\Params\Listing\Design;


use App\Models\Attribute;
use App\OpenApi\Params\Listing\ListThingBaseParams;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'ShowAttributeParams')]
class ShowAttributeParams extends ListThingBaseParams
{

    public function __construct(
        protected ?Attribute $given_attribute = null
    )
    {
        parent::__construct();
    }


    #[OA\Property(title: 'Type Detail',description: 'Increase to show more type information')]
    protected int $owning_type_levels = 0;

    #[OA\Property(title: 'Design detail',description: 'Increase to show more information about the design')]
    protected int $design_levels = 0;

    #[OA\Property(title: 'Attribute detail',description: 'Increase to show more information about the attribute')]
    protected int $attribute_levels = 1;



    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col,$do_validation);

        $this->owning_type_levels = static::naturalFromCollection(collection: $col,param_name: 'owning_type_levels');
        $this->design_levels = static::naturalFromCollection(collection: $col,param_name: 'design_levels');
        $this->attribute_levels = static::naturalFromCollection(collection: $col,param_name: 'attribute_levels');


    }

    public  function toArray() : array  {
        $what = parent::toArray();
        $what['owning_type_levels'] = $this->owning_type_levels;
        $what['design_levels'] = $this->design_levels;
        $what['attribute_levels'] = $this->attribute_levels;
        return $what;
    }

    public function getGivenAttribute(): ?Attribute
    {
        return $this->given_attribute;
    }

    public function getOwningTypeLevels(): int
    {
        return $this->owning_type_levels;
    }

    public function getDesignLevels(): int
    {
        return $this->design_levels;
    }

    public function getAttributeLevels(): int
    {
        return $this->attribute_levels;
    }





}
