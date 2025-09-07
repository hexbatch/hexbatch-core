<?php

namespace App\OpenApi\Params\Listing\Design;


use App\Models\ElementType;
use App\OpenApi\Params\Listing\ListThingBaseParams;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'ShowDesignParams')]
class ShowDesignParams extends ListThingBaseParams
{

    public function __construct(
        protected ?ElementType     $given_type = null
    )
    {
        parent::__construct();
    }

    #[OA\Property(title: 'Namespace Detail',description: 'Increase to show more namespace information')]
    protected int $namespace_levels = 0;

    #[OA\Property(title: 'Parent detail',description: 'Increase to show more information about the parents')]
    protected int $parent_levels = 0;

    #[OA\Property(title: 'Attribute detail',description: 'Increase to show more information about the attributes')]
    protected int $attribute_levels = 1;

    #[OA\Property(title: 'Attribute parent detail',description: 'Increase to show more attribute parent details')]
    protected int $inherited_attribute_levels = 0;

    #[OA\Property(title: 'Number time spans',description: 'Increase to show more than one')]
    protected int $number_time_spans = 1;



    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col,$do_validation);

        $this->namespace_levels = static::naturalFromCollection(collection: $col,param_name: 'namespace_levels');
        $this->parent_levels = static::naturalFromCollection(collection: $col,param_name: 'parent_levels');
        $this->attribute_levels = static::naturalFromCollection(collection: $col,param_name: 'attribute_levels');
        $this->inherited_attribute_levels = static::naturalFromCollection(collection: $col,param_name: 'inherited_attribute_levels');
        $this->number_time_spans = static::naturalFromCollection(collection: $col,param_name: 'number_time_spans');


    }

    public  function toArray() : array  {
        $what = parent::toArray();
        $what['namespace_levels'] = $this->namespace_levels;
        $what['parent_levels'] = $this->parent_levels;
        $what['attribute_levels'] = $this->attribute_levels;
        $what['inherited_attribute_levels'] = $this->inherited_attribute_levels;
        $what['number_time_spans'] = $this->number_time_spans;
        return $what;
    }

    public function getNamespaceLevels(): int
    {
        return $this->namespace_levels;
    }

    public function getParentLevels(): int
    {
        return $this->parent_levels;
    }

    public function getAttributeLevels(): int
    {
        return $this->attribute_levels;
    }

    public function getInheritedAttributeLevels(): int
    {
        return $this->inherited_attribute_levels;
    }

    public function getNumberTimeSpans(): int
    {
        return $this->number_time_spans;
    }

    public function getGivenType(): ?ElementType
    {
        return $this->given_type;
    }





}
