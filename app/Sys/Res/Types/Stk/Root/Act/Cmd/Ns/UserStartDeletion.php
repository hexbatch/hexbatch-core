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
use App\Sys\Res\Types\Stk\Root\Marker\DeletingUserMarker;
use Hexbatch\Thangs\Interfaces\ICommandCallable;

#[HexbatchTitle( title: "Starts the deletion of the user, and all its namespaces")]
#[HexbatchBlurb( blurb: "This will give permission for the user to delete itself")]
#[HexbatchDescription( description: "
  This permission can be blocked by an event
")]
class UserStartDeletion extends Act\Cmd\Ns implements ICommandCallable
{
    use Traits\NamespaceStartAction;
    const UUID = 'fe677c59-7ebe-4a0d-ba3e-4cba4ef13c08';
    const ACTION_NAME = TypeOfAction::CMD_USER_START_DELETION;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Us::class,
    ];

    const EVENT_CLASSES = [
        Evt\Element\UserDeletionPreparing::class
    ];

    const string|INewSystemType MARKER_CLASS = DeletingUserMarker::class;
    const string|IElementEvent|null PRE_EVENT_CLASS = Evt\Element\UserDeletionPreparing::class;

}

