<?php

namespace App\Sys\Res\Types\Stk\Root\Signal;



use App\Sys\Res\Types\BaseType;


/**
 The master semaphore has 4 types that it uses, each will be derived from the types below
 The first type will be inheriting from this,
 * the @uses \App\Sys\Res\Types\Stk\Root\NamespaceType of the logged in ns
 * and perhaps others,such as @uses \App\Sys\Res\Types\Stk\Root\Signal\Master\Remote
 *
 * That master type will have semaphores inheriting from it and @uses Semaphore
 *  Unless its given another semaphore type to use instead, this would allow master semaphores to be chained
 *   which can be used if a remote wants to be contacted, then give a response later
 *   which will be handled without rules processing the middle part of the request chain
 *
 * it will have a response inheriting from that master type and
 * @uses \App\Sys\Res\Types\Stk\Root\Signal\Master\MasterResponse
 *
 * It will have an outer set from that master type and @uses \App\Sys\Res\Types\Stk\Root\Signal\Master\OuterSetType
 * there is only one set here for the lifetime of the type
 *
 * It will have an action set from that master type and @uses \App\Sys\Res\Types\Stk\Root\Signal\Master\ActionSetType
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
 * it will fill in the wait raw table with the semaphore type and the new response
 * it will then put the semaphore element in idle again
 *
 * Then its in pause mode for that call (nothing happens,  not a mode)
 *
 * Then it will wait for someone to put the response element from the type made above, into the action set
 * The Action set will have an @uses SetEnter event handler, (only allowing that one element to enter) and when that happens
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
 */
class MasterSemaphore extends BaseType
{
    const UUID = '5f03b8f4-1e28-491a-bfad-3e941a8fb8b3';
    const TYPE_NAME = 'master_semaphore';



    const ATTRIBUTE_CLASSES = [
//todo add attribute to set the number of semaphores
    //todo add attribute for if the action sets should be destroyed after the things waiting on it have completed
    ];

    const PARENT_CLASSES = [
        Semaphore::class
    ];

}

