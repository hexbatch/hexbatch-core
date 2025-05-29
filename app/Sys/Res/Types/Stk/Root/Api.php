<?php

namespace App\Sys\Res\Types\Stk\Root;


use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\UserNamespace;
use App\OpenApi\Callbacks\HexbatchCallbackCollectionResponse;
use App\OpenApi\Callbacks\HexbatchCallbackResponse;
use App\OpenApi\ErrorResponse;
use App\Sys\Res\Namespaces\Stock\ThisNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;
use Hexbatch\Things\Enums\TypeOfCallback;
use Hexbatch\Things\Enums\TypeOfCallbackStatus;
use Hexbatch\Things\Enums\TypeOfHookMode;
use Hexbatch\Things\Interfaces\ICallResponse;
use Hexbatch\Things\Interfaces\IHookCode;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\Models\ThingCallback;
use Hexbatch\Things\Models\ThingHook;
use Hexbatch\Things\OpenApi\Callbacks\CallbackSearchParams;
use Hexbatch\Things\OpenApi\Errors\ThingErrorResponse;
use Hexbatch\Things\OpenApi\Hooks\HookParams;
use Hexbatch\Things\OpenApi\Hooks\HookSearchParams;
use Hexbatch\Things\OpenApi\Things\ThingResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as CodeOf;


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

    const FAMILY_CLASSES = [
        Api::class,
        Api\DesignApi::class,
        Api\ElementApi::class,
        Api\ElsewhereApi::class,
        Api\NamespaceApi::class,
        Api\OperationApi::class,
        Api\PathApi::class,
        Api\PhaseApi::class,
        Api\ServerApi::class,
        Api\SetApi::class,
        Api\TypeApi::class,
        Api\UserApi::class,
        Api\WaitingApi::class
    ];

    public function __construct(
        protected ?ActionDatum   $action_data = null,
        protected ?UserNamespace $owner_namespace = null,
        protected bool           $b_type_init = false,
        protected bool           $is_system = false,
        protected bool           $send_event = true,
        protected ?bool           $is_async = null,
        protected int            $priority = 0,
        protected array          $tags = []
    )
    {
        if (!$this->owner_namespace ) {
            if ($this->action_data?->data_owner_namespace) {
                $this->owner_namespace = $this->action_data->data_owner_namespace;
            }
        }
        if (!$this->owner_namespace ) {
            $this->owner_namespace = Utilities::getCurrentNamespace();
        }

        if (!$this->owner_namespace) {
            $this->owner_namespace = Utilities::getSystemNamespace();
        }

        // always the top of the food chain, so never has a parent data structure
        parent::__construct(action_data: $this->action_data, owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,priority: $this->priority,tags: $this->tags);

    }


    public static function runHook(ThingCallback $callback,Thing $thing,ThingHook $hook,array $header, array $body): ICallResponse
    {
        return new ThingResponse(thing:$thing);
    }

    public function onNextStepB(): void
    {
        parent::onNextStepB();
        if (in_array(static::class,static::FAMILY_CLASSES)) {return;}

        $search_params = new HookSearchParams(action_type: static::class, is_blocking: true, is_after: true);
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
            $owner = Utilities::getThisUserDefaultNamespace();
        }
        if (!$owner) {
            $owner = Utilities::getSystemNamespace();
        }
        return Thing::buildFromAction(action: $this,owner: $owner,extra_tags: $tags );
    }


    public function getCallbackResponse(?int &$http_status = null)
    : null|array|HexbatchCallbackCollectionResponse|HexbatchCallbackResponse|ThingErrorResponse|ThingResponse|ErrorResponse
    {
        if (!$this->getActionData()) {return null;}
        $search_params = new CallbackSearchParams(
            is_blocking: true,
            is_after: true,
            thing_action_type: static::class,
            thing_action_id: $this->getActionData()->id);

        //$search_params->setHookOwner(ThisNamespace::getCreatedNamespace());
        /** @var ThingCallback[] $maybe_callbacks */
        $maybe_callbacks = ThingCallback::buildCallback(params: $search_params)->get();
        if (count($maybe_callbacks) === 0) {
            //find the thing tied to this api
            $http_status = CodeOf::HTTP_OK;
            try {
                $thing = Thing::getThing(action_type_id: $this->getActionId(), action_type: $this->getActionType());
                return new ThingResponse(thing: $thing);
            } catch (\Exception $e) {
                Log::warning(sprintf("Could not find thing for %s / %s of ",$this->getActionType(),$this->getActionId()) );
                return ErrorResponse::fromException(e: $e);
            }
        }
        if (count($maybe_callbacks) === 1) {
            $http_status = $maybe_callbacks[0]->callback_http_code;
            if ($maybe_callbacks[0]->callback_error) {
                return new ThingErrorResponse(error: $maybe_callbacks[0]->callback_error);
            }
            if ($maybe_callbacks[0]->thing_callback_status === TypeOfCallbackStatus::CALLBACK_SUCCESSFUL) {
                return $maybe_callbacks[0]->callback_incoming_data->getArrayCopy();
            }
            return new HexbatchCallbackResponse(callback: $maybe_callbacks[0]);

        }
        $http_status = 0;
        foreach ($maybe_callbacks as $callback) {
            if ($callback->callback_http_code > $http_status) { $http_status = $callback->callback_http_code;}
        }
        return new HexbatchCallbackCollectionResponse(given_callbacks: $maybe_callbacks);
    }

}

