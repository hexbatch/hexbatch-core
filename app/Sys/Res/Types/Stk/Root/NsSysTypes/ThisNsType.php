<?php

namespace App\Sys\Res\Types\Stk\Root\NsSysTypes;

use App\Exceptions\HexbatchInitException;
use App\Models\ActionDatum;
use App\Models\UserNamespace;
use App\Sys\Res\Ele\Stk\SystemNS\SystemHandleElement;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\BasePerNamespace;

class ThisNsType extends BaseType
{
    const TYPE_NAME = 'system_namespace';

    public static function getClassUuid() : string {
        $name = config('hbc.system.namespace.types.ns_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace type uuid is not set in .env");
        }
        return $name;
    }

    const HANDLE_ELEMENT_CLASS = SystemHandleElement::class;


    const PARENT_CLASSES = [
        BasePerNamespace::class,
    ];

    public function __construct(
        protected ?ActionDatum   $action_data = null,
        protected ?int           $action_data_parent_id = null,
        protected ?int           $action_data_root_id = null,
        protected ?UserNamespace $owner = null,
        protected bool           $b_type_init = false,
        protected bool         $is_system = false,
        protected bool         $send_event = true,

    )
    {
        parent::__construct(action_data: $this->action_data,
            action_data_parent_id: $this->action_data_parent_id, action_data_root_id: $this->action_data_root_id,
            owner: $this->owner, b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event);

    }

}

