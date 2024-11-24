<?php

namespace App\Helpers\Actions;

use App\Enums\Rules\TypeMergeJson;
use App\Enums\Rules\TypeOfLogic;
use App\Sys\Res\Types\ISystemType;
use Illuminate\Support\Collection;


class ActionNode
{

    protected string|ISystemType $action_class;
    protected TypeOfLogic $action_child_logic;
    protected TypeOfLogic $action_logic;
    protected TypeMergeJson $merge_method;

    /** @var ActionNode[] $action_children  */
    protected array $action_children = [];

    protected ?Collection $collection = null;

    /**
     * @param ActionNode[] $action_children
     */
    public function __construct(string $action_class,
                                ?Collection $collection = null,
                                TypeOfLogic $action_child_logic = TypeOfLogic::AND,
                                TypeOfLogic $action_logic = TypeOfLogic::AND,
                                TypeMergeJson $merge_method = TypeMergeJson::OR_MERGE,
                                array $action_children = []

    )
    {
        $this->action_class = $action_class;
        $this->action_child_logic = $action_child_logic;
        $this->action_logic = $action_logic;
        $this->merge_method = $merge_method;
        $this->action_children = $action_children;
        $this->collection = $collection;
    }


    public function getActionClass(): string|ISystemType
    {
        return $this->action_class;
    }

    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    public function setActionClass(string $action_class): ActionNode
    {
        $this->action_class = $action_class;
        return $this;
    }

    public function getActionChildLogic(): TypeOfLogic
    {
        return $this->action_child_logic;
    }

    public function setActionChildLogic(TypeOfLogic $action_child_logic): ActionNode
    {
        $this->action_child_logic = $action_child_logic;
        return $this;
    }

    public function getActionLogic(): TypeOfLogic
    {
        return $this->action_logic;
    }

    public function setActionLogic(TypeOfLogic $action_logic): ActionNode
    {
        $this->action_logic = $action_logic;
        return $this;
    }

    public function getMergeMethod(): TypeMergeJson
    {
        return $this->merge_method;
    }

    public function setMergeMethod(TypeMergeJson $merge_method): ActionNode
    {
        $this->merge_method = $merge_method;
        return $this;
    }

    /** @return ActionNode[] */
    public function getActionChildren(): array
    {
        return $this->action_children;
    }

    public function setActionChildren(array $action_children): ActionNode
    {
        $this->action_children = $action_children;
        return $this;
    }




}
