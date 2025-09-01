<?php

namespace App\OpenApi\Params\Listing\Set;


use App\Models\ElementSet;
use App\OpenApi\Params\Listing\ListDataBaseParams;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'ShowSetParams')]
class ShowSetParams extends ListDataBaseParams
{

    public function __construct(
        protected ?ElementSet $given_set = null
    )
    {

    }

    #[OA\Property(title: 'Type Detail',description: 'Increase to show more type information')]
    protected int $definer_type_level = 0;

    #[OA\Property(title: 'Children set detail',description: 'Increase to show more information about the children')]
    protected int $children_set_level = 0;

    #[OA\Property(title: 'Parent set detail',description: 'Increase to show more information about the parent')]
    protected int $parent_set_level = 0;

    #[OA\Property(title: 'Show elements')]
    protected bool $show_elements = false;

    #[OA\Property(title: 'Show definer')]
    protected bool $show_definer = false;

    #[OA\Property(title: 'Show parent')]
    protected bool $show_parent = false;




    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col,$do_validation);

        $this->definer_type_level = static::naturalFromCollection(collection: $col,param_name: 'definer_type_level');
        $this->children_set_level = static::naturalFromCollection(collection: $col,param_name: 'children_set_level');
        $this->parent_set_level = static::naturalFromCollection(collection: $col,param_name: 'parent_set_level');
        $this->show_parent = static::boolFromCollection(collection: $col,param_name: 'show_parent');
        $this->show_definer = static::boolFromCollection(collection: $col,param_name: 'show_definer');
        $this->show_elements = static::boolFromCollection(collection: $col,param_name: 'show_elements');


    }

    public  function toArray() : array  {
        $what = parent::toArray();
        $what['definer_type_level'] = $this->definer_type_level;
        $what['children_set_level'] = $this->children_set_level;
        $what['parent_set_level'] = $this->parent_set_level;
        $what['show_parent'] = $this->show_parent;
        $what['show_definer'] = $this->show_definer;
        $what['show_elements'] = $this->show_elements;
        return $what;
    }

    public function getGivenSet(): ?ElementSet
    {
        return $this->given_set;
    }

    public function getDefinerTypeLevel(): int
    {
        return $this->definer_type_level;
    }

    public function getChildrenSetLevel(): int
    {
        return $this->children_set_level;
    }

    public function getParentSetLevel(): int
    {
        return $this->parent_set_level;
    }

    public function isShowElements(): bool
    {
        return $this->show_elements;
    }

    public function isShowDefiner(): bool
    {
        return $this->show_definer;
    }

    public function isShowParent(): bool
    {
        return $this->show_parent;
    }







}
