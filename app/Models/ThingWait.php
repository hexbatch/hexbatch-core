<?php

namespace App\Models;


use App\Enums\Things\TypeOfThingWaitPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * note: how to use the thing collections for signals and remote
 * for signals
 * when the thing runs, fill in row thing_waits using the signal type and possible expiration
 * use this to find the rows waiting for this event, and to cancel events. If a mutex has all elements destroyed,
 * then the things waiting on that will return false.
 *
 * for remotes, there will be an element this call is assigned to,
 * the element is of the semaphore and remote type children and is one of the semaphore elements
 *  regardless if the remote is an in, out or both: before the call is made,
 *   the semaphore is assigned from the semaphore_waiting, to the remote call waiting group,
 *   if there are none in waiting then remote fails
 *
 *
 *  its up to the remote to make the semaphore element available to the waiting thing
 *   (so
 *     the remote grabs the semaphore from the wait group, if not ready will wait,
 *      clears out the set of older call data
 *      before the command returns to the thing
 *          it puts the element into the semaphore_idle group,
 *          the thing_waits is filled out for the thing calling, the remote type and this semaphore element associated with this call
 *
 *      and moves the semaphore element to waiting when the response happens.
 *   )
 *  the remote's semaphore element defines a set of its own, where the current call and response elements are located
 *  the thing can find that call response data inside the set of the semaphore element it was waiting on
 *
 *   if the remote fails, then it removes the thing_waits row for that thing and the thing will return false to its parent
 *   either for success or fail, and is put in waiting set. Failure is call timed out
 *
 *   for rules, it can wait on this semaphore, without a remote call from a child, this is totally ok,
 *     there is no call data to process, so anything processing it will return error, and perhaps side logic needed
 *
 *
 * note: when a mutex element is destroyed, find any rows waiting for that type in the thing_waits , and make them be false to the parent
 *
 * note: things waiting are marked as paused
 *
 * todo when thing runs looking for the next batch of rows to run, do the check on the thing_waits every so often
 *  and make those expired rows return false to parent, and clean up resources for remote sets
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int waiting_thing_id
 * @property int waiting_on_type_id
 * @property int waiting_with_element_id
 * @property string expires_at

 * @property TypeOfThingWaitPolicy thing_waiting_policy

 *
 *
 */
class ThingWait extends Model
{


    protected $table = 'thing_waits';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'thing_waiting_policy' => TypeOfThingWaitPolicy::class,
    ];

}
