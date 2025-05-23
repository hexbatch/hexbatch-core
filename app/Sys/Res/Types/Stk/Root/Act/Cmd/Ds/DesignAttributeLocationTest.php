<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;


/**
 * Can be tested against another type, attribute or geo-jason
 *
 */
class DesignAttributeLocationTest extends Act\Cmd\Ds
{
    const UUID = '1f104a48-34f4-4338-9723-a62fccbbe83a';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_LOCATION_TEST;
//
    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
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
            owner: $this->owner, b_type_init: $this->b_type_init,
            is_system: $this->is_system, send_event: $this->send_event, action_data_parent_id: $this->action_data_parent_id,
            action_data_root_id: $this->action_data_root_id);

    }

}

