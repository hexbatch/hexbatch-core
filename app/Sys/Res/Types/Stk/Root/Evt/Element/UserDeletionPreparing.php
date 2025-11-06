<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Element\Traits\ElementBlockingEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


#[HexbatchTitle( title: "Blocking event for starting user deletion")]
#[HexbatchBlurb( blurb: "Some accounts can be protected from deletion, or otherwise have actions done first")]
#[HexbatchDescription( description: "
  Fired from the user pre deletion action before the permission element is put into the home set
")]
class UserDeletionPreparing extends Evt\ScopeElement implements ICommandCallable, Traits\IElementEvent
{
    use ElementBlockingEventTree;

    const UUID = '3faf234e-1eca-47f8-b915-4e823a91a305';
    const EVENT_NAME = TypeOfEvent::USER_DELETION_PREPARING;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($children_args,$command_args,'starting user deletion');
    }

    protected function decide() : bool {
        return true;
    }

}

