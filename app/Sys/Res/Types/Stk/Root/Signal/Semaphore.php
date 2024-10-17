<?php

namespace App\Sys\Res\Types\Stk\Root\Signal;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Signal;

/*

type semaphore inherits from signal
    it has attributes child can make own from:
         max_time_to_wait_seconds default null
         is_automatically_waiting : default false
         seconds_in_wait_idle : default null
         when_put_in_wait : default null

 this has N elements for however many semaphores can be used at once
 elements start in the global set semaphore_idle unless the automatic flag is set,
then they go into semaphore_waiting.
 todo The command semaphore_ready will move the path elements to the semaphore_waiting from semaphore_idle
 todo the command semaphore_reset will move the path elements the opposite way from waiting to idle,
  and anything waiting on those elements will signal false to the parent thing

 when waiting to be used, the semaphores are in the global set semaphore_waiting,
   this set allows all elements of the semaphore type to be there, but nothing else.

   if seconds_in_wait_idle is used, then the semaphore cannot be claimed until its wait time is up,
 this allows for minimum pauses between events

   if the type defines the max_time_to_wait, then the wait will be cancelled,
and the thing row sends false to its parent

 todo new event semaphore_wait -
  does much like the above, just different global set to find the semaphore semaphore_waiting.
    also can wait for multiple semaphore types, or different elements from the same type,
     all of which must be ready;
    when done the semaphore elements go back to the idle or waiting, based on the auto attribute


 todo new event semaphore_wait_any - like above,
    but first semaphore free in the target path will allow rule to run, rest are ignored

 note: if you need to wait for any mutex, make them a semaphore with only one element
 note: to cast votes make semaphore element for each vote, to start an election then move all votes to unused.
 	In the semaphore type do action in rule to move to waiting when value of attribute set,
         using the write event for the vote being done
 	elsewhere have something waiting for all the votes to be in active before it runs, using the semaphore_wait.
 	This way things can be done when elements are written to in some combination.
 	The other option is to use a remote to count the changes, then toggle a manual remote.
 	This way, everything is defined in the rules and less tampering

 */

class Semaphore extends BaseType
{
    const UUID = '635d3b10-55bf-4528-9d86-673b3fdc7211';
    const TYPE_NAME = 'signal_semaphore';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Signal::UUID
    ];

}

