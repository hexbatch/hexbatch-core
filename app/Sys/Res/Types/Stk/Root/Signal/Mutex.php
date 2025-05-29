<?php

namespace App\Sys\Res\Types\Stk\Root\Signal;

use App\Sys\Res\Atr\Stk\Signal\Mutex\OncePerLifetime;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Signal;

/*
 type mutex, inherit from signal, allows for one thing to be done at a time
    it has attributes child can make own from:
     once_per_lifetime default false


each mutex type only has one element, other elements cannot be made,

mutexes are only allowed to be in two set types. Each element can only be in one set at a time
	global system set mutex_waiting is where mutexes that are not claimed are at. There is only one set of this per server.
	set type local_signal_claimed is where a thing collection root can put mutexes into when they are claimed by the rules.
	This is made when the rule is waiting, and deleted when the rule is done
    if once_per_lifetime then the element for the mutex is destroyed and nothing can use it until another element is made
A mutex for use inherits from the base mutex and the ns type,  a normal type creation


new rule actions
event WAIT_MUTEX  - will claim mutex, moving it from the mutex_waiting set, if not there will wait indefinitely
                     When there its put it into set type local_signal_claimed at the thing node that is waiting
                     when thing node is done, the set is destroyed and the claimed is put into mutex_waiting again.
                     It is automatically released after thing row is done, even after indefinite time.
                     Then the next wait can grab it.
                     can wait for multiple mutexes at once if the path finds more than one to use for the rule,
                      all the mutexes must be ready
                      or mutex use of types can be nested in the rule chain
                     can wait for ancestor of mutex but not for system type

WAIT_AVAILABLE - will return false for the thing row if the mutex is not available when the thing row runs,
                            otherwise it is like the above
                            works on semaphores too

note: mutex_or_bust can make sure only one event handler will process for any fired event

 */

class Mutex extends BaseType
{
    const UUID = 'ce614965-912f-4e57-b866-2d3fd73ff000';
    const TYPE_NAME = 'signal_mutex';



    const ATTRIBUTE_CLASSES = [
        OncePerLifetime::class
    ];

    const PARENT_CLASSES = [
        Signal::class
    ];

}

