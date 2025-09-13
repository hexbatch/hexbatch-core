<?php

namespace App\OpenApi\Params\Listing\Elements;


use App\Models\Element;
use App\OpenApi\Params\Listing\ListThingBaseParams;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'ShowElementParams')]
class ShowElementParams extends ListThingBaseParams
{

    public function __construct(
        protected ?Element $given_element = null
    )
    {
        parent::__construct();
    }

    #[OA\Property(title: 'Type Detail',description: 'Increase to show more type information')]
    protected int $type_level = 0;

    #[OA\Property(title: 'Namespace detail',description: 'Increase to show more information about the namespace')]
    protected int $namespace_level = 0;

    #[OA\Property(title: 'Attribute detail',description: 'Increase to show more information about the attribute')]
    protected int $attribute_level = 0;

    #[OA\Property(title: 'Attribute detail',description: 'Increase to show more information about the phase')]
    protected int $phase_level = 0;



    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col,$do_validation);

        $this->type_level = static::naturalFromCollection(collection: $col,param_name: 'type_level');
        $this->namespace_level = static::naturalFromCollection(collection: $col,param_name: 'namespace_level');
        $this->attribute_level = static::naturalFromCollection(collection: $col,param_name: 'attribute_level');
        $this->phase_level = static::naturalFromCollection(collection: $col,param_name: 'phase_level');


    }

    public  function toArray() : array  {
        $what = parent::toArray();
        $what['type_level'] = $this->type_level;
        $what['namespace_level'] = $this->namespace_level;
        $what['attribute_level'] = $this->attribute_level;
        $what['phase_level'] = $this->phase_level;
        return $what;
    }

    public function getGivenElement(): ?Element
    {
        return $this->given_element;
    }

    public function getTypeLevel(): int
    {
        return $this->type_level;
    }

    public function getNamespaceLevel(): int
    {
        return $this->namespace_level;
    }

    public function getAttributeLevel(): int
    {
        return $this->attribute_level;
    }

    public function getPhaseLevel(): int
    {
        return $this->phase_level;
    }






}
