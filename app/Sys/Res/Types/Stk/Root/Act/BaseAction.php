<?php

namespace App\Sys\Res\Types\Stk\Root\Act;

use App\Enums\Sys\TypeOfAction;
use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\UserNamespace;
use App\Sys\Res\Atr\Stk\Act\ActionMetric;
use App\Sys\Res\IAction;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Action;


class BaseAction extends BaseType implements IAction
{
    const UUID = 'ebdcbddd-c746-44dc-84b0-cf1f8f174b2b';
    const ACTION_NAME = TypeOfAction::BASE_ACTION;

    public function __construct(
        protected ?ActionDatum   $action_data = null,
        protected ?ActionDatum   $parent_action_data = null,
        protected ?UserNamespace $owner_namespace = null,
        protected bool           $b_type_init = false,
        protected bool           $is_system = false,
        protected bool           $send_event = true,
        protected int            $priority = 0,
        protected array          $tags = []
    )
    {
        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,
            owner_namespace: $this->owner_namespace, b_type_init: $this->b_type_init,
            is_system: $this->is_system, send_event: $this->send_event,priority: $this->priority,tags: $this->tags);
        Utilities::ignoreVar(static::ACTION_NAME,static::getHexbatchClassName());
    }

    public static function getHexbatchClassName() :string { return static::ACTION_NAME->value; }


    const ATTRIBUTE_CLASSES = [
        ActionMetric::class
    ];

    const PARENT_CLASSES = [
        Action::class
    ];

    const EVENT_CLASSES = [];


    public static function getRelatedEvents(): array
    {
        return static::EVENT_CLASSES;
    }

    public static function getActionTypeStatic(): string
    {
        return static::ACTION_NAME->value;
    }
}

