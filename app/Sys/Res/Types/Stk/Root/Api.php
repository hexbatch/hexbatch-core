<?php

namespace App\Sys\Res\Types\Stk\Root;


use App\Models\ActionDatum;
use App\Models\UserNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;
use Hexbatch\Things\Interfaces\ICallResponse;
use Hexbatch\Things\Interfaces\IHookCode;


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
        protected ?ActionDatum $action_data = null,
        protected ?UserNamespace $owner = null,
        protected bool $b_type_init = false,
        protected bool         $is_system = false,
        protected bool         $send_event = true,
        protected ?int         $action_data_parent_id = null,
        protected ?int         $action_data_root_id = null,
    )
    {
        parent::__construct(action_data: $this->action_data,
            action_data_parent_id: $this->action_data_parent_id, action_data_root_id: $this->action_data_root_id,
            owner: $this->owner, b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event);

        //todo check if after blocking hook is setup for this static type, if not make one
    }


    public static function runHook(array $header, array $body): ICallResponse
    {
        throw new \LogicException("not implemented");
    }

}

