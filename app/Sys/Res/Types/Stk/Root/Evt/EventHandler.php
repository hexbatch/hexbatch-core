<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementLink;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\Server;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Event;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Facades\Log;


class EventHandler extends Event implements ICommandCallable
{
    const UUID = 'd11c69dc-43d6-4ffd-9f40-9801cee05424';

    const EVENT_NAME = TypeOfEvent::EVENT_HANDLER;


    const PARENT_CLASSES = [
        Event::class
    ];

    public function __construct(
        protected string   $ref,
        protected ?ElementType    $type_context = null,
        protected ?UserNamespace    $namespace_context = null,
        protected ?Attribute $attribute_context = null,
        protected ?ElementSet $set_context = null,
        protected ?Element        $element_context = null,
        protected ?Server         $elsewhere_context = null,
        protected ?Phase $phase_context = null,
        protected ?ElementLink $link_context = null,
        protected mixed $important_value = null,
        protected array $important_array = []
    )
    {

    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called event handler");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }


}

