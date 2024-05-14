<?php

namespace App\Models;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Jobs\RunRemoteStack;
use App\Models\Enums\Remotes\RemoteActivityStatusType;
use App\Models\Enums\Remotes\RemoteStackCategoryType;
use App\Models\Enums\Remotes\RemoteStackLogicType;
use App\Models\Enums\Remotes\RemoteStackStatusType;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int user_id
 * @property int parent_remote_stack_id
 * @property string ref_uuid
 * @property int level_from_top
 * @property int child_priority_level
 * @property ArrayObject children_data
 * @property ArrayObject ending_activity_data
 * @property ArrayObject starting_activity_data
 * @property ArrayObject ending_data
 * @property ArrayObject error_data
 * @property string created_at
 * @property string updated_at
 * @property string stack_ended_at
 *
 * @property RemoteStackCategoryType remote_stack_category
 * @property RemoteStackStatusType remote_stack_status
 * @property RemoteStackLogicType remote_stack_logic_type
 *
 * @property User stack_owner
 * @property RemoteStack parent_stack
 * @property RemoteActivity[] children_activities
 * @property RemoteStack[] children_stacks
 *
 */
class RemoteStack extends Model
{

    protected $table = 'remote_stacks';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stack_ended_at'
    ];

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
        'remote_stack_category' => RemoteStackCategoryType::class,
        'remote_stack_status' => RemoteStackStatusType::class,
        'remote_stack_logic_type' => RemoteStackLogicType::class,
        'children_data' => AsArrayObject::class,
        'ending_data' => AsArrayObject::class,
        'error_data' => AsArrayObject::class,
        'ending_activity_data' => AsArrayObject::class,
        'starting_activity_data' => AsArrayObject::class,
    ];
    public function stack_owner(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function parent_stack(): BelongsTo
    {
        $what =  $this->belongsTo('App\Models\RemoteStack', 'parent_remote_stack_id');
        static::decorateBuilder($what);
        return $what;
    }

    public function children_activities() : hasMany {
        return $this->hasMany('App\Models\RemoteActivity','remote_stack_id','id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts")
            /** @uses RemoteActivity::remote_parent() */
            ->with('remote_parent');
    }

    public function children_stacks() : hasMany {
        $what =  $this->hasMany('App\Models\RemoteStack','parent_remote_stack_id','id');
        static::decorateBuilder($what);
        return $what;
    }



    public static function decorateBuilder(Builder|Relation $build) :void {
        $build->select('remote_stacks.*')
            ->selectRaw(" extract(epoch from  remote_stacks.created_at) as created_at_ts,".
                "  extract(epoch from  remote_stacks.updated_at) as updated_at_ts")
            /** @uses RemoteStack::parent_stack(),RemoteStack::stack_owner(),RemoteStack::children_activities() */
            ->with('parent_stack','stack_owner','children_activities')
        ;
    }

    /**
     * @param RemoteStackStatusType[] $status_types
     * @param RemoteStackCategoryType[] $category_types
     */
    public static function buildRemoteStack(
        ?int $id = null,false|int|null $parent_id = false,array $category_types = [],array $status_types = [],
        ?User $owner = null
    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = RemoteStack::where('id','>','0');

        static::decorateBuilder($build);

        if ($id) {
            $build->where('remote_stacks.id', $id);
        }

        if ($parent_id !== false) {
            if ($parent_id) {
                $build->where('remote_stacks.parent_remote_stack_id', $parent_id);
            } else {
                $build->whereNull('remote_stacks.parent_remote_stack_id');
            }

        }

        if ($owner) {
            $build->where('remote_stacks.user_id', $owner->id);
        }


        if (count($category_types)) {
            $string_cats = [];
            foreach ($category_types as $cat_type) {
                $string_cats[] = $cat_type->value;
            }
            $build->whereIn('remote_stacks.remote_stack_category', $string_cats);
        }

        if (count($status_types)) {
            $string_statuses = [];
            foreach ($status_types as $status_type) {
                $string_statuses[] = $status_type->value;
            }
            $build->whereIn('remote_stacks.remote_stack_status', $string_statuses);
        }

        return $build;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $build = null;
        $ret = null;
        $first_id = null;
        try {
            if ($field) {
                $build = $this->where($field, $value);
            } else {
                if (Utilities::is_uuid($value)) {
                    //the ref
                    $build = $this->where('ref_uuid', $value);
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = RemoteStack::buildRemoteStack(id:$first_id)->first();
                }
            }
        } finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.stack_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::REMOTE_STACK_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    public function getName() :string {
        return $this->ref_uuid;
    }


    public function run_stack() {
        if ( $this->remote_stack_status !== RemoteStackStatusType::PENDING) {
            throw new \RuntimeException("Stack #$this->id status is not pending, its ". $this->remote_stack_status->value);
        }
        $this->remote_stack_status = RemoteStackStatusType::STARTED;
        $this->save();
        foreach ($this->children_activities as $child) {
            if ($this->remote_stack_category !== RemoteStackCategoryType::MAIN) {
               $starting_data = array_merge(($this->parent_stack?->ending_data->getArrayCopy())??[],($this->starting_activity_data?->getArrayCopy())??[]);
            } else {
                $starting_data = ($this->starting_activity_data?->getArrayCopy())??[];
            }
            if (!empty($this->starting_activity_data)) {
                $child->to_remote_processed_data = array_merge($starting_data,($child->to_remote_processed_data?->getArrayCopy())??[]);
                $child->save();
            }
            $child->runActivity();
        }
    }

    /**
     * @throws \Exception
     */
    public function execute_stack(RemoteStackCategoryType $category) {

        try {
            //if there are main children that have not run yet, do those first
            /**
             * @var RemoteStack[]|\Illuminate\Database\Eloquent\Collection $main_children
             */
            $main_children = static::buildRemoteStack(parent_id: $this->id, category_types: [RemoteStackCategoryType::MAIN], status_types: [RemoteStackStatusType::NONE])->get();
            if (count($main_children)) {
                foreach ($main_children as $mainy) {
                    $mainy->execute_stack($mainy->remote_stack_category); //will execute the others when done
                }
                return;
            }
            if ($category === RemoteStackCategoryType::MAIN) {
                if ($this->remote_stack_status !== RemoteStackStatusType::NONE) {
                    return;
                } //in middle

                //only run if the main children are all finished
                $still_running_children = static::buildRemoteStack(parent_id: $this->id, category_types: [RemoteStackCategoryType::MAIN],
                    status_types: [RemoteStackStatusType::NONE, RemoteStackStatusType::PENDING, RemoteStackStatusType::STARTED])->get();
                if (count($still_running_children)) {
                    //last child finishing will call this
                    return;
                }

                //if any child has error then this is error and go up to the next parent
                $error_children = static::buildRemoteStack(parent_id: $this->id, category_types: [RemoteStackCategoryType::MAIN],
                    status_types: [RemoteStackStatusType::ERROR])->get();
                if (count($error_children)) {
                    $this->remote_stack_status = RemoteStackStatusType::ERROR;
                    $this->save();
                    try {
                        //call parent to start, if not started
                        if ($this->parent_stack) {
                            $this->parent_stack->execute_stack(RemoteStackCategoryType::MAIN);
                        }
                    } catch (\Exception $e) {
                        $this->addError($e);
                    }
                    return;
                }

                $this->remote_stack_status = RemoteStackStatusType::PENDING;
                $this->save();
                RunRemoteStack::dispatch($this->id);
                return;
            }

            //run all the other children at the same time for each cat
            /**
             * @var RemoteStack[]|\Illuminate\Database\Eloquent\Collection $children
             */
            $children = static::buildRemoteStack(parent_id: $this->id, category_types: [$category], status_types: [RemoteStackStatusType::NONE])->get();

            foreach ($children as $child) {
                $child->remote_stack_status = RemoteStackStatusType::PENDING;
                $child->save();
                RunRemoteStack::dispatch($child->id);
            }
        } catch (\Exception $e) {
            $this->addError($e);
            throw $e;
        }

    }

    public function stack_finalization()
    {

        try {
            foreach ($this->children_activities as $act) {
                if (!in_array($act->remote_activity_status_type, [RemoteActivityStatusType::CACHED, RemoteActivityStatusType::FAILED, RemoteActivityStatusType::SUCCESS])) {
                    return;
                }
            }
            $failed = [];
            $sucess = [];
            $combined_data = [];
            foreach ($this->children_activities as $act) {
                if (in_array($act->remote_activity_status_type, [RemoteActivityStatusType::CACHED, RemoteActivityStatusType::SUCCESS])) {
                    $sucess[] = $act;
                } else {
                    $failed[] = $act;
                }
                $combined_data = array_merge_recursive($combined_data, $act->from_remote_processed_data->getArrayCopy());
            }

            //do the logic and call parent stack function to further do stuff
            if ($this->remote_stack_logic_type === RemoteStackLogicType::ALL_MUST_SUCCEED) {
                if (count($failed)) {
                    $this->remote_stack_status = RemoteStackStatusType::FAILED;
                } else {
                    if (count($sucess)) {
                        $this->remote_stack_status = RemoteStackStatusType::SUCCESS;
                    } else {
                        $this->remote_stack_status = RemoteStackStatusType::FAILED;
                    }

                }

            } else if ($this->remote_stack_logic_type === RemoteStackLogicType::SOME_FAILING_OK) {
                if (count($sucess)) {
                    $this->remote_stack_status = RemoteStackStatusType::SUCCESS;
                } else {
                    $this->remote_stack_status = RemoteStackStatusType::FAILED;
                }
            } else {
                throw new \RuntimeException("Stack logic ". $this->remote_stack_logic_type->value ." unknown for stack # ".$this->id);
            }
            $this->ending_activity_data = $combined_data;
            $this->ending_data = array_merge_recursive($this->children_data->getArrayCopy(),$combined_data);
            if ($this->parent_stack) {
                $this->parent_stack->children_data = array_merge_recursive($this->parent_stack->children_data->getArrayCopy(),$combined_data);
            }
            $this->save();
            $this->update(['stack_ended_at' => DB::raw('NOW()')]);
            //was this a main entry?, if so run the others based on status here
            if ($this->remote_stack_category === RemoteStackCategoryType::MAIN) {
                if ($this->remote_stack_status === RemoteStackStatusType::SUCCESS) {
                    $this->execute_stack(RemoteStackCategoryType::ON_SUCCESS);
                    //if this is the final node of the stack, the system put in an on success php remote call to do the stuff after the stack finished
                } else {
                    $this->execute_stack(RemoteStackCategoryType::ON_FAILURE);
                }
                $this->execute_stack(RemoteStackCategoryType::ON_ALWAYS);

            }
        } catch (\Exception $e) {
            $this->addError($e);
        }

        try {
            //call parent to start, if not started
            if ($this->parent_stack) {
                $this->parent_stack->execute_stack(RemoteStackCategoryType::MAIN);
            }
        } catch (\Exception $e) {
            $this->addError($e);
        }
    }

    protected function addError(\Exception $e) {
        $this->remote_stack_status = RemoteStackStatusType::ERROR;
        $node = ['message'=>$e->getMessage(),'class'=>get_class($e)];
        Log::error("Remote stack error",$node);
        if (is_array($this->error_data) && count($this->error_data)) {
            $this->error_data[] = $node;
        } else {
            $this->error_data = $node;
        }
        $this->save();
    }
}
