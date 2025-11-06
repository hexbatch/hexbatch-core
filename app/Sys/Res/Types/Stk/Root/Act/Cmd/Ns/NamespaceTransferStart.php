<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\INewSystemType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Element\Traits\IElementEvent;
use App\Sys\Res\Types\Stk\Root\Marker\TransferNamespaceMarker;
use Hexbatch\Thangs\Interfaces\ICommandCallable;

#[HexbatchTitle( title: "Starts the transfer of ownership of namespaces")]
#[HexbatchBlurb( blurb: "Any namespace can be transferred except for a user's default namespace")]
#[HexbatchDescription( description: "
  Transfers are non destructive actions, either the entire ns, and all its belongings are transferred, or nothing happens.
  The new user can block this by having a listener in their default ns. If the event is blocked, no permission element is set.
  Otherwise a permission element is set with a uuid that needs to be given in the next command
")]
class NamespaceTransferStart extends Act\Cmd\Ns implements ICommandCallable
{
    use Traits\NamespaceStartAction;
    const UUID = '57de6229-9f68-4bda-a1bf-613d06b742cd';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_PREP_TRANSFER;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

    const EVENT_CLASSES = [
        Evt\Element\NamespaceStartingTransfer::class
    ];

    const string|INewSystemType MARKER_CLASS = TransferNamespaceMarker::class;
    const string|IElementEvent|null PRE_EVENT_CLASS = Evt\Element\NamespaceStartingTransfer::class;

}

