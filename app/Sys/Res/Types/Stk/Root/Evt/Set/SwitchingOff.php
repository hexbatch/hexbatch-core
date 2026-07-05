<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Element\Traits\ElementBlockingEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;


class SwitchingOff extends Evt\ScopeSet  implements ICommandCallable
{
    use ElementBlockingEventTree;

    const UUID = 'ca462f72-13f6-4acc-8670-6380cef18244';
    const EVENT_NAME = TypeOfEvent::SWITCHING_OFF;


    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

    public function __construct(
        protected Element             $given_element,
        protected ?ElementSet                   $given_set
    )
    {

    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($children_args,$command_args,'turning off');
    }

    protected function decide() : bool {
        return true;
    }

    /**
     * @throws \Throwable
     */
    public static function callEventTree(
        Element               $given_element,
        ?ElementSet             $given_set,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder|null
    {
        return static::callEventTreeInner(given_element: $given_element,given_set: $given_set,builder: $builder);
    }

}

