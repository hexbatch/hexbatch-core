<?php

namespace App\Sys\Res\Types\Stk\Root\Signal;



use App\Sys\Res\Atr\Stk\Signal\Semaphore\IsAutomaticallyWaiting;
use App\Sys\Res\Atr\Stk\Signal\Semaphore\MaxWaitSeconds;
use App\Sys\Res\Atr\Stk\Signal\Semaphore\SecondsWaitIdle;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Signal;

/*

type semaphore inherits from signal
     it has attributes child can make own from:
         max_wait_seconds default null
         is_automatically_waiting : default false
         seconds_wait_idle : default null

 this has N elements for however many semaphores can be used at once
 elements start in the global set semaphore_idle unless the automatic flag is set,
then they go into semaphore_waiting.
  The command CMD_SEMAPHORE_READY will move the path elements to the semaphore_waiting from semaphore_idle
  the command CMD_SEMAPHORE_RESET will move the path elements the opposite way from waiting to idle,
  and anything waiting on those elements will signal false to the parent thing

 when waiting to be used, the semaphores are in the global set semaphore_waiting,
   this set allows all elements of the semaphore type to be there, but nothing else.

   if seconds_in_wait_idle is used, then the semaphore cannot be claimed until its wait time is up,
 this allows for minimum pauses between events

   if the type defines the max_time_to_wait, then the wait will be canceled,
and the thing row sends false to its parent

 waiting on the semaphore
  does much like the above, just different global set to find the semaphore semaphore_waiting.
    also can wait for multiple semaphore types, or different elements from the same type,
     all of which must be ready;
    when done the semaphore elements go back to the idle or waiting, based on the auto attribute


  wait_any - like above,
    but first semaphore free in the target path will allow rule to run, rest are ignored

WAIT_AVAILABLE works on semaphores, will return false if not currently waiting on that semaphore type


 note: if you need to wait for any mutex, make them a semaphore with only one element
 note: to cast votes make semaphore element for each vote, to start an election then move all votes to unused.
 	In the semaphore type do action in rule to move to waiting when value of attribute set,
         using the write event for the vote being done
 	elsewhere have something waiting for all the votes to be in active before it runs, using the semaphore_wait.
 	This way things can be done when elements are written to in some combination.
 	The other option is to use a remote to count the changes, then toggle a manual remote.
 	This way, everything is defined in the rules and less tampering.

-------------------------------------------------------------------------------------------
 master semaphore notes: from deleted class structure
         * The master semaphore has 4 types that it uses, each will be derived from the types below
         * The first type will be inheriting from this,
         * the @uses \App\Sys\Res\Types\Stk\Root\NamespaceType of the logged in ns
         * and perhaps others,such as @uses \App\Sys\Res\Types\Stk\Root\Signal\Semaphore\Master\Remote
         *
         * That master type will have semaphores inheriting from it and @uses Semaphore
         *  Unless its given another semaphore type to use instead, this would allow master semaphores to be chained
         *   which can be used if a remote wants to be contacted, then give a response later;
         *   which will be handled without rules processing the middle part of the request chain
         *
         * it will have a response inheriting from that master type and
         * @uses \App\Sys\Res\Types\Stk\Root\Signal\Semaphore\Master\MasterResponse
         *
         * It will have an outer set from that master type and @uses \App\Sys\Res\Types\Stk\Root\Signal\Semaphore\Master\OuterSetType
         * there is only one set here for the lifetime of the type
         *
         * It will have an action set from that master type and @uses \App\Sys\Res\Types\Stk\Root\Signal\Semaphore\Master\OuterSet\ActionSetType
         * There will be a new action set made for each run
         *
         * All the newly created types share the same element has a handle, which is created from the base type given to make the others
         * ----------------------------------------------------------
         * When this is all created there are N semaphores in the
         * @uses SemaphoreIdleSetType
         * or
         * @uses SemaphoreWaitingSetType
         *
         * if all in idle, will wait for a semaphore
         * it will make a new action set, and a new response
         * the new response goes into the @uses WaitingResponseSetType set
         * it will fill in the wait raw table with the semaphore type and the new response;
         * it will then put the semaphore element in idle again
         *
         * Then its in pause mode for that call (nothing happens,  not a mode)
         *
         * Then it will wait for someone to put the response element from the type made above, into the action set
         * The Action set will have an @uses SetEntering event handler, (only allowing that one element to enter) and when that happens
         * the rules will call the @uses SemaphoreReady to make it go to the ready set,
         * and the @uses \App\Models\ThingWait will wake up the thing(s) that are waiting on this
         *
         * People can make this to have the incoming remotes, they simply put the element they fill out  into the action set
         * For outgoing remotes, then have the master type also include remote, and then after the pause
         * then
         *
         * when called in a set context, then any bounds the master semaphore has from the base type its given will be used
         *
 *
 *
 *

 */




class Semaphore extends BaseType
{
    const UUID = '635d3b10-55bf-4528-9d86-673b3fdc7211';
    const TYPE_NAME = 'signal_semaphore';



    const ATTRIBUTE_CLASSES = [
        MaxWaitSeconds::class,
        IsAutomaticallyWaiting::class,
        SecondsWaitIdle::class,
    ];

    const PARENT_CLASSES = [
        Signal::class
    ];

}

