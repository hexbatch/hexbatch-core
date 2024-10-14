<?php

namespace App\Models;


use App\Enums\Things\TypeOfThingWaitPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * note: how to use the thing collections for signals and remote
 * for signals
 * when the thing runs, fill in row thing_waits using the signal type and possible expiration
 * use this to find the rows waiting for this event, and to cancel events. If a mutex has all elements destroyed, then the things waiting on that will return false.
 *
 * for remotes, these can be outgoing or incoming or outgoing|incoming.
 * If incoming, then waiting on a remote type, the type is put into the waiting, then matched up when the manually set remote is filled out by an api
 *
 * if outgoing, we make the call and make the result set
 * each call, even to the same remote, has its own set at the root, the rule paths can figure out the correct remote if more than one of the same kind made
 * with the remote set made at the root, we record the result type for the remote type , and the remote set, in the waiting
 * for each parent of the thing that makes the call, and is listening for that remote result type, we put that parent into the waiting
 * (and not the calling thing, it passes execution to its parent after the remote call)
 * when the remote answers and its result element is made, and put into the set
 *  then we have some hard coded stuff to look up the waiting and remove the waiting row, then when parent is ready to execute it can
 *
 *
 * if a remote is outgoing , but has a delayed response, then the response is manually set as the incoming, but that is mapped to the wait here,
 * so still waiting on the response element
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
 * @property int waiting_with_set
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
