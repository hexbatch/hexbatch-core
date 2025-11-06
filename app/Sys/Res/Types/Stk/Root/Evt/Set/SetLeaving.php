<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;


class SetLeaving extends Evt\ScopeSet implements ICommandCallable
{
    use Evt\Set\Traits\SetBlockingEventTree;

    const UUID = '21104b44-14fc-44e3-a632-80113d48988d';
    const EVENT_NAME = TypeOfEvent::SET_LEAVING;


    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

    public function __construct(
        protected ElementSet            $given_set,
        protected Element             $given_element

    )
    {

    }



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($children_args,$command_args,'set leaving');
    }

    protected function decide() : bool {
        return true;
    }

    /**
     * @throws \Throwable
     */
    public static function callEventTree(
        ElementSet            $given_set,
        Element               $given_element,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder|null
    {
        return static::callEventTreeInner($given_set,$given_element,$builder);
    }

}

