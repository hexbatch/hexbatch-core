<?php

namespace App\Sys\Res\Types\Stk\Root;


use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\UserNamespace;
use App\OpenApi\ApiCallBase;
use App\OpenApi\ErrorResponse;
use App\OpenApi\Results\Callbacks\HexbatchCallbackCollectionResponse;
use App\OpenApi\Results\Callbacks\HexbatchCallbackResponse;
use App\Sys\Res\Namespaces\Stock\ThisNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;
use Hexbatch\Things\Enums\TypeOfCallback;
use Hexbatch\Things\Enums\TypeOfCallbackStatus;
use Hexbatch\Things\Enums\TypeOfHookMode;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\ICallResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\Models\ThingCallback;
use Hexbatch\Things\Models\ThingHook;
use Hexbatch\Things\OpenApi\Callbacks\CallbackSearchParams;
use Hexbatch\Things\OpenApi\Errors\ThingErrorCollectionResponse;
use Hexbatch\Things\OpenApi\Errors\ThingErrorResponse;
use Hexbatch\Things\OpenApi\Hooks\HookParams;
use Hexbatch\Things\OpenApi\Hooks\HookSearchParams;
use Hexbatch\Things\OpenApi\Things\ThingResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as CodeOf;


/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Api extends BaseType implements ICallResponse
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
            b_type_init: $this->b_type_init, is_system: $this->is_system,
            send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);

    }




    public function onNextStepB(): void
    {
        parent::onNextStepB();
        if (in_array(static::class,static::FAMILY_CLASSES)) {return;}
        //see if implements hook
        $interfaces = class_implements($this);

        if (!isset($interfaces['Hexbatch\Things\Interfaces\IHookCode'])) {
            return;
        }

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
        $this->my_thing =  Thing::buildFromAction(action: $this,owner: $owner,extra_tags: $tags );
        return $this->my_thing;
    }

    protected ?Thing $my_thing = null;
    protected function getMyThing() :?Thing {
        if ($this->my_thing) {return $this->my_thing;}
        $this->my_thing =   Thing::getThing(action_type_id: $this->getActionId(), action_type: $this->getActionType());
        return $this->my_thing;
    }

    public function getCallbackResponse(?int &$http_status = null)
    : null|array|HexbatchCallbackCollectionResponse|HexbatchCallbackResponse|ThingErrorResponse|ThingResponse|
    ErrorResponse|ThingErrorCollectionResponse|ApiCallBase|IThingBaseResponse
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

            try {
                $thing = $this->getMyThing();
                if ($thing->isSuccess()) {
                    $http_status = static::HTTP_CODE_GOOD;
                    return $this->getDataSnapshot();
                }
                else if($thing->isError()) {
                    $http_status = static::HTTP_CODE_ERROR;
                    return new ThingErrorCollectionResponse(thing: $thing);
                }
                else if($thing->isFailedOrError()) {
                    $http_status = static::HTTP_CODE_BAD;
                    return new ThingErrorCollectionResponse(thing: $thing);
                } else {
                    $http_status = static::HTTP_CODE_PENDING;
                    return new ThingResponse(thing: $thing); //punt in case missed something
                }

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
                $http_status = static::HTTP_CODE_GOOD;
                return $this->getDataSnapshot();
            }
            return new HexbatchCallbackResponse(callback: $maybe_callbacks[0]);

        }
        $http_status = 0;
        foreach ($maybe_callbacks as $callback) {
            if ($callback->callback_http_code > $http_status) { $http_status = $callback->callback_http_code;}
        }
        return new HexbatchCallbackCollectionResponse(given_callbacks: $maybe_callbacks);
    }


    public function getInitialConstantData(): array {
        $ret = parent::getInitialConstantData();
        if (property_exists($this, 'params')) {
            $interfaces = class_implements($this->params);

            if (isset($interfaces['App\Sys\Res\Types\Stk\Root\Api\IApiParam'])) {
                $ret['api_params'] = $this->params;
            }
        }
        return $ret;
    }

    protected function restoreParams(array $param_array) {

    }

    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data->collection_data?->offsetExists('api_params')) {
            $param_array = $this->action_data->collection_data->offsetGet('api_params');
            $this->restoreParams($param_array);
        }
    }

    const int HTTP_CODE_GOOD = CodeOf::HTTP_ACCEPTED;
    const int HTTP_CODE_ERROR = CodeOf::HTTP_UNPROCESSABLE_ENTITY;
    const int HTTP_CODE_SHORT = CodeOf::HTTP_GONE;
    const int HTTP_CODE_PENDING = CodeOf::HTTP_OK;
    const int HTTP_CODE_BAD = CodeOf::HTTP_BAD_REQUEST;

    public function getCode(): int
    {
        switch ($this->getActionStatus()) {
            case TypeOfThingStatus::THING_SUCCESS: {
                return static::HTTP_CODE_GOOD;
            }

            case TypeOfThingStatus::THING_SHORT_CIRCUITED:
            {
                return static::HTTP_CODE_SHORT;
            }
            case TypeOfThingStatus::THING_INVALID:
            case TypeOfThingStatus::THING_FAIL:
            {
                return static::HTTP_CODE_BAD;
            }

            case TypeOfThingStatus::THING_BUILDING:
            case TypeOfThingStatus::THING_RUNNING:
            case TypeOfThingStatus::THING_WAITING:
            case TypeOfThingStatus::THING_PENDING:
            {
                return static::HTTP_CODE_PENDING;
            }

            case TypeOfThingStatus::THING_ERROR: {
                return static::HTTP_CODE_ERROR;
            }
        }
        throw new \LogicException("Unknown status: ". $this->getActionStatus()->value);

    }

    public function getData(): ?array
    {
        if ($this->isActionSuccess()) {
            return $this->getDataSnapshot();
        } else if ($this->isActionFail() || $this->isActionError()) {
            return $this->getDataSnapshot();
        }

       return null;
    }

    public function getWaitTimeoutInSeconds(): ?int
    {
        return null;
    }

    public static function runHook(ThingCallback $callback,Thing $thing,ThingHook $hook,array $header, array $body): ICallResponse
    {
        Utilities::ignoreVar($callback,$hook,$header,$body);
        $me = $thing->getAction();
        /** @type ICallResponse */
        return $me;
    }

    const PRIMARY_SNAPSHOT_KEY = 'override_me';

}

