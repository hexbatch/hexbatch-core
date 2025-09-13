<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\UserNamespace;
use App\OpenApi\Params\Actioning\Design\DesignTimeParams;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * Can be given another type to copy the schedule from
 */
#[HexbatchTitle( title: "Destroys a schdule")]
#[HexbatchBlurb( blurb: "Time bounds can removed if not used by any published type")]
#[HexbatchDescription( description:'')]
class DesignLocationDestroy extends DesignLocationCreate
{
    const UUID = 'f6986ecb-de5e-4551-86cf-2cbc855b9780';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LOCATION_DESTROY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];
    #[ApiParamMarker( param_class: DesignTimeParams::class)]
    public function __construct(
        protected ?string           $given_location_uuid = null,
        protected bool              $is_system = false,
        protected bool              $send_event = true,
        protected ?bool             $is_async = null,
        protected ?ActionDatum      $action_data = null,
        protected ?ActionDatum      $parent_action_data = null,
        protected ?UserNamespace    $owner_namespace = null,
        protected bool                $b_type_init = false,
        protected array          $tags = []
    )
    {

        parent::__construct(given_location_uuid: $this->given_location_uuid, is_deleting: true,
            is_system: $this->is_system, send_event: $this->send_event,
            is_async: $this->is_async,
            action_data: $this->action_data, parent_action_data: $this->parent_action_data, owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init,  tags: $this->tags);
    }

}

