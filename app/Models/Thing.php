<?php

namespace App\Models;



use App\Enums\Rules\TypeMergeJson;
use App\Enums\Rules\TypeOfLogic;
use App\Enums\Things\TypeOfThingStatus;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;

/*
 * thing is marked as done when all children done, and there is no pagination id
 */
/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_thing_id
 * @property int after_thing_id
 * @property int api_call_type_id
 * @property int thing_server_event_id
 *
 * @property int thing_pagination_size
 * @property int thing_pagination_limit
 * @property int thing_depth_limit
 * @property int thing_rate_limit
 * @property int thing_backoff_policy
 * @property int thing_json_size_limit
 *
 *
 * @property int thing_rank
 * @property string thing_start_after
 * @property string thing_invalid_at
 * @property string ref_uuid
 * @property ArrayObject thing_value
 * @property TypeOfThingStatus thing_status
 * @property TypeOfLogic thing_child_logic
 * @property TypeOfLogic thing_logic
 * @property TypeMergeJson thing_merge_method
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property ThingDatum[] thing_collection
 */
class Thing extends Model
{

    protected $table = 'pending_things';
    public $timestamps = false;

    /**
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     *
     * @var array<string, string>
     */
    protected $casts = [
        'thing_value' => AsArrayObject::class,
        'thing_status' => TypeOfThingStatus::class,
        'thing_logic' => TypeOfLogic::class,
        'thing_child_logic' => TypeOfLogic::class,
        'thing_merge_method' => TypeMergeJson::class,
    ];


    public function thing_collection() : HasMany {
        return $this->hasMany(ThingDatum::class,'owning_thing_id','id');
    }

    /*
     * the response to each event from the api is determined here, because we have no way to know if this is immediate or delayed return
     *  it may be a direct return, or the user may have a callback, or its polled later
     *  so, need a structured way to match responses, and data gathering for them, to finished events
     *  todo  each event/api to have its own class and interface, with setters for the input data, and getter for the response data
     */


    public static function buildThing(
        ?int $id = null,
        ?int $rule_id = null,
        ?int $server_event_id = null,
    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build =  Thing::select('things.*')
            ->selectRaw(" extract(epoch from  things.created_at) as created_at_ts,  extract(epoch from  things.updated_at) as updated_at_ts")
        ;

        if ($id) {
            $build->where('things.id',$id);
        }

        if ($rule_id) {
            $build->where('things.thing_rule_id',$rule_id);
        }



        if ($server_event_id) {


            $build->join('attribute_rules',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($server_event_id) {
                    $join
                        ->on('attribute_rules.id','=','things.thing_rule_id')
                        ->where('owning_server_event_id',$server_event_id);
                }
            );
        }

        /**
         * @uses Thing::thing_collection()
         */
        $build->with('thing_collection');

        return $build;
    }

}
