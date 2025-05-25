<?php

namespace App\Sys\Res\Types\Stk\Root;


use App\Models\ActionDatum;
use App\Models\UserNamespace;
use App\Sys\Res\Namespaces\Stock\ThisNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;
use Hexbatch\Things\Enums\TypeOfCallback;
use Hexbatch\Things\Enums\TypeOfHookMode;
use Hexbatch\Things\Interfaces\ICallResponse;
use Hexbatch\Things\Interfaces\IHookCode;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\Models\ThingHook;
use Hexbatch\Things\OpenApi\Hooks\HookParams;
use Hexbatch\Things\OpenApi\Hooks\HookSearchParams;


/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Api extends BaseType implements IHookCode
{
    const UUID = 'd314149a-0f51-4b1e-b954-590a890e7c44';
    const TYPE_NAME = 'api';




    const PARENT_CLASSES = [
        Root::class
    ];

    public function __construct(
        protected ?ActionDatum   $action_data = null,
        protected ?UserNamespace $owner_namespace = null,
        protected bool           $b_type_init = false,
        protected bool           $is_system = false,
        protected bool           $send_event = true,
        protected int            $priority = 0,
        protected array          $tags = []
    )
    {
        // always the top of the food chain, so never has a parent data structure
        parent::__construct(action_data: $this->action_data, owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,priority: $this->priority,tags: $this->tags);

    }


    public static function runHook(array $header, array $body): ICallResponse
    {
        throw new \LogicException("hook not implemented for api ". static::getHexbatchClassName());
    }

    public function onNextStep(): void
    {
        parent::onNextStep();
        $search_params = new HookSearchParams(action_type: static::getHexbatchClassName(), is_blocking: true, is_after: true);
        $maybe_hook = ThingHook::buildHook(params: $search_params)->first();
        if (!$maybe_hook) {
            $params = new HookParams(
                mode: TypeOfHookMode::NODE,
                name: "after-". static::getHexbatchClassName(),
                notes: "created in api constructor",
                action_type: static::class,
                hook_on: true,
                is_blocking: true,
                is_after: true,
                callback_type: TypeOfCallback::CODE,
                address: static::class
            );
            $params->setHookOwner(ThisNamespace::getCreatedNamespace());
            ThingHook::createHook($params);
        }
    }


    /**
     * @throws \Exception
     */
    public function createThingTree(array $tags = []) : Thing {
        $owner = $this->getDataOwner();
        if (!$owner) {
            $owner = ThisNamespace::getCreatedNamespace();
        }
        return Thing::buildFromAction(action: $this,owner: $owner,extra_tags: $tags );
    }

}

