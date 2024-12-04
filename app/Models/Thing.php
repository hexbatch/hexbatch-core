<?php

namespace App\Models;


use App\Api\Calls\IApiThingSetup;
use App\Enums\Rules\TypeOfMergeLogic;
use App\Enums\Rules\TypeOfLogic;
use App\Enums\Things\TypeOfThingDataSource;
use App\Enums\Things\TypeOfThingStatus;
use App\Jobs\RunThing;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\ApiMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Build\BuildApiFacet;
use App\Sys\Res\Types\Stk\Root\Act\NoEventsTriggered;
use App\Sys\Res\Types\Stk\Root\Act\SystemPrivilege;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as CodeOf;

/**
 * thing is marked as done when all children done, and there is no pagination id
 *
 * When there is a full page for a container, the parent makes a cursor row in the data
   * A new child thing is made for using that next page of data , the child may start later according to the backoff
    * the child results combined or_all to the parent. Empty data for the last cursor is child success too.
   * the thing parent cannot complete until all the new children return success.
 * todo when an action has @see NoEventsTriggered up-type, then its not going to add preconditions by event listeners
 *   there is no permission check, if the action has it, regardless who is calling then no events
 *
 * todo the thing will have a list built and updated by the hbc:system that allows code (and not db) checks for action inheritance
 *   for @see SystemPrivilege there is a permission check but events can be called
 *
 * todo the thing function that accepts the params @see IApiThingSetup for notes
 *
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_thing_id
 * @property int after_thing_id
 * @property int api_or_action_type_id
 * @property int thing_rule_id
 * @property int thing_phase_id
 *
 *
 *
 * @property int thing_rank
 * @property int debugging_breakpoint
 * @property bool is_waiting_on_hook
 *
 * @property string thing_start_after
 * @property string thing_invalid_at
 * @property string process_started_at
 * @property string ref_uuid
 * @property TypeOfThingStatus thing_status
 * @property TypeOfLogic thing_child_logic
 * @property TypeOfLogic thing_logic
 * @property TypeOfMergeLogic thing_merge_method_json
 * @property TypeOfMergeLogic thing_merge_method_data
 *
 * @property string created_at
 * @property int created_at_ts
 * @property string updated_at
 *
 * @property ThingDatum[] thing_collection
 * @property ThingResult thing_result
 * @property Thing thing_parent
 * @property Thing[]|\Illuminate\Database\Eloquent\Collection thing_children
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
        'thing_status' => TypeOfThingStatus::class,
        'thing_logic' => TypeOfLogic::class,
        'thing_child_logic' => TypeOfLogic::class,
        'thing_merge_method_json' => TypeOfMergeLogic::class,
        'thing_merge_method_data' => TypeOfMergeLogic::class,
    ];


    public function thing_collection() : HasMany {
        return $this->hasMany(ThingDatum::class,'owning_thing_id','id');
    }

    public function thing_children() : HasMany {
        return $this->hasMany(Thing::class,'parent_thing_id','id');
    }

    public function thing_parent() : BelongsTo {
        return $this->belongsTo(Thing::class,'parent_thing_id','id');
    }

    public function thing_result() : HasOne {
        return $this->hasOne(ThingResult::class,'owner_thing_id','id')
            /** @uses ThingResult::hex_error() */
            ->with('hex_error');
    }


    public function getThingResponseJson() : array {
        if ($this->thing_result->hex_error) {
            return $this->thing_result->hex_error->getErrorJson();
        }
        return $this->thing_result->result_response->getArrayCopy();
    }

    public function getThingResponseHttpCode() : int {
        if (!$this->isComplete()) {
            return CodeOf::HTTP_ACCEPTED;
        }
        return $this->thing_result->result_http_status??($this->thing_result->hex_error? CodeOf::HTTP_UNPROCESSABLE_ENTITY: CodeOf::HTTP_OK );
    }

    public function isComplete() : bool {
        if ($this->thing_status === TypeOfThingStatus::THING_SUCCESS || $this->thing_status === TypeOfThingStatus::THING_ERROR) {
            return true;
        }
        return false;
    }


    public function isSuccess() : bool {
        //see if flag already set, if so, then do not re-evaluate
        if ($this->thing_status === TypeOfThingStatus::THING_ERROR) {
            return false;
        }

        if ($this->thing_status === TypeOfThingStatus::THING_SUCCESS) {
            return true;
        }

        //status not set
        //maybe its always true or always false
        if ($this->thing_logic === TypeOfLogic::ALWAYS_FALSE) {return false;}
        if ($this->thing_logic === TypeOfLogic::ALWAYS_TRUE) {return true;}
        // see what is up with the data!


        //if this has empty data, then it is an error
        $current_data = $this->getCurrentData();
        if (empty($current_data)) { return false;}

        $true_count = 0;
        $false_count = 0;
        foreach ($current_data as $datum) {
            if ($datum->collection_json === null) {continue;}
            if ($datum->isJsonFalse()) { $false_count++;} else { $true_count++;}
        }
        //if there is data (passed check above) and no countable json then success
        if (empty($true_count) && empty($false_count)) {return true;}

        //now use the self logic to see what is up with these counts!
        return static::doLogic($this->thing_logic,$true_count,$false_count);
    }

    public static function doLogic(TypeOfLogic $logic,int $true_count, int $false_count) {
        return match($logic) {
            TypeOfLogic::AND => $false_count && $true_count,
            TypeOfLogic::OR, TypeOfLogic::OR_ALL => $false_count || $true_count,
            TypeOfLogic::XOR => $false_count xor $true_count,
            TypeOfLogic::NAND => !($false_count && $true_count),
            TypeOfLogic::NOR, TypeOfLogic::NOR_ALL => !($false_count || $true_count),
            TypeOfLogic::XNOR => !($false_count xor $true_count),
            TypeOfLogic::NOP, TypeOfLogic::ALWAYS_TRUE => true,
            TypeOfLogic::ALWAYS_FALSE => false
        };
    }

    /**
     * @return ThingDatum[]
     */
    public function getCurrentData() : array  {
        $ret = [];
        foreach ($this->thing_collection as $datum) {
            if ($datum->thing_data_source === TypeOfThingDataSource::FROM_CURRENT) { $ret[] = $datum;}
        }
        return $ret;
    }

    /**
     * @return array[]
     */
    public function getCurrentJson() : array  {
        $ret = [];
        foreach ($this->getCurrentData() as $datum) {
            if ($datum->collection_json !== null) {
                $ret[] = $datum->collection_json->getArrayCopy();
            }
        }
        return $ret;
    }

    /**
     * @return Thing[]
     */
    public function getLeaves() {
        return [];
        //todo get the leaves
    }

    public function changeStatusAll(TypeOfThingStatus $status) {
        //todo change for this and all descendents
    }

    public function setProcessedAt() {
        //todo update the process_started_at to now
    }

    public function setException(\Exception $e) {
        //todo set this exception to the thing result (make one if not there already)
        $hex = HexError::createFromException($e);
    }

    public function setCurrentData(array $arr) {
        //todo set this current data and do logic with child data before maybe setting use from_current source
    }

    /**
     * to be called in @uses IApiThingResult::processChildrenData(), IActionParams::processChildrenData()
     */
    public function setChildrenData(array $arr) {
        //todo set this children data and do logic with data before maybe setting use from_children source

    }

    public function checkChildLogic() : ?bool {
        if (!count($this->thing_children)) {return true;} //if no children then always true for child logic

        $true_count = 0;
        $false_count = 0;
        //go through all the children, see their status and return null if any are not complete
        foreach ($this->thing_children as $child) {
            if (!$child->isComplete()) {return null;}
            if ($child->isSuccess()) { $true_count++;} else { $false_count++;}

        }
        return static::doLogic($this->thing_child_logic,$true_count,$false_count);
    }

    public function getMergedJson() : ?array {

        $working = [];
        $current = $this->getCurrentJson();
        if (empty($current)) {return null;}
        switch ($this->thing_merge_method_json) {
            case TypeOfMergeLogic::UNION:
            case TypeOfMergeLogic::INTERSECTION:
            case TypeOfMergeLogic::DIFFERENCE:
            {
                $working = $current;
                break;
            }
        }
        return TypeOfMergeLogic::mergeArrays($this->thing_merge_method_json,$working);
    }

    /**
     * todo use the pulled data function in action and api handlers
     * returns the logic merged child data and the merged json
     * @return ThingDatum[]
     */
    public function pullChildData(array &$json) : array {

        $json = [];

        //merge child json to one json
        //merge child data to array but do not write
        $working = [];
        $current = $this->thing_children;
        switch ($this->thing_merge_method_json) {
            case TypeOfMergeLogic::UNION:
            case TypeOfMergeLogic::INTERSECTION:
            case TypeOfMergeLogic::DIFFERENCE:
            {
                $working = $current;
                break;
            }
        }

        $all_json = [];
        foreach ($working as $child) {
            $json_of_child = $child->getMergedJson();
            if ($json_of_child === null) {continue;}
            $all_json[] = $json_of_child;
        }
        $json =  TypeOfMergeLogic::mergeArrays($this->thing_merge_method_json,$all_json);

        $all_stuff = [];
        foreach ($working as  $childer) {
            $stuff = $childer->getCurrentData();
            if (!empty($stuff)) {
                $all_stuff[] = $stuff;
            }
        }

        return TypeOfMergeLogic::mergeArrays($this->thing_merge_method_json,$all_stuff);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function runThing() {
        try {
            DB::beginTransaction();
            //todo check the child logic, see if can run or just fail here
            $child_logic = $this->checkChildLogic();
            if ($child_logic !== null) {
                if (!$child_logic) {
                    $this->thing_status = TypeOfThingStatus::THING_ERROR;
                }
            }

            if ($this->thing_status !== TypeOfThingStatus::THING_ERROR) {
                // child logic allows it to be processed
                //todo pull children data up to this data, if any, merge the children data together and put in from _children source
                // (how to decide which children data to put in from_children)? (missing step)
                // if any child json then do json merge logic

                //see if action or api or attribute rule
                $type = ElementType::getElementType(id: $this->api_or_action_type_id);
                if (is_subclass_of($type, 'App\Sys\Res\Types\Stk\Root\Api')) {
                    /** @var \App\Api\Calls\IApiThingResult $result */
                    $result = ApiMapper::getApiInterface(BuildApiFacet::FACET_RESPONSE, $type::getClassUuid());
                    $result->processChildrenData($this);
                    $result->writeReturn($this->thing_result);
                } elseif (is_subclass_of($type, 'App\Sys\Res\Types\Stk\Root\Act\BaseAction')) {
                    /** @var \App\Api\Cmd\IActionWorker $work */
                    $work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER, $type::getClassUuid());

                    /** @var \App\Api\Cmd\IActionParams $params */
                    $params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS, $type::getClassUuid());

                    $params->processChildrenData($this);
                    $work_results = $work::doWork($params);
                    $work_results->toThing($this);

                } else {
                    throw new \LogicException("Thing has neither an action or api: $type->ref_uuid " . $type->getName());
                }
                $this->thing_status = TypeOfThingStatus::THING_SUCCESS;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->thing_status = TypeOfThingStatus::THING_ERROR;
            $this->save();
            throw $e;
        }
    }

    public function maybeQueueParent() : bool {
        foreach ($this->thing_children as $thang) {
            if (!$thang->isComplete()) {return false;}
        }
        if ($this->thing_parent) {
            RunThing::dispatch($this->thing_parent);
        }
        return true;
    }



    public function pushLeavesToJobs() {
        if ($this->thing_status !== TypeOfThingStatus::THING_BUILDING) {
            throw new \LogicException("Cannot push what is already built");
        }
        $this->changeStatusAll(TypeOfThingStatus::THING_PENDING);
        foreach ($this->getLeaves() as $leaf) {
            RunThing::dispatch($leaf);
        }

    }

    /**
     *
     * todo when api success then need to update the response with the facet for the API response
     *
     * todo any request can have a user callback uri set in the collection,
     *  so create the result row for the api call when this is made, and set the result callback if that url is set
     */
    public static function makeApiCall(string $uuid,Collection $collection,?Phase $phase = null) : Thing {

        $root = new Thing();

        $api_call_type = ElementType::getElementType(uuid: $uuid);
        $root->api_or_action_type_id = $api_call_type->id;

        if (!$phase) {$phase = Phase::getDefaultPhase();}
        $root->thing_phase_id = $phase->id;
        $root->save();
        /** @var \App\Api\IApiOaParams $params */
        $params = ApiMapper::getApiInterface(BuildApiFacet::FACET_PARAMS,$uuid);
        $params->fromCollection($collection);

        /** @var IApiThingSetup $setup */
        $setup = ApiMapper::getApiInterface(BuildApiFacet::FACET_SETUP,$uuid);
        $setup->setupDataWithThing($root, $params);
        $actions = $setup->getActions();

        foreach ($actions as $action) {
            static::makeAction($root,$action);
        }
        return static::getThing(id:$root->id);

    }

    public static function makeAction(Thing $parent_thing,\App\Helpers\Actions\ActionNode $action_node = null) : Thing {

        //todo the events need to be made a child node for any action, and that tree has to be completed, so the events must be children that must all be completed (and)
        // if any events are after , then those are siblings of this action, with the after_thing_id set to this action thing

        $node = new Thing();
        $node->parent_thing_id = $parent_thing->id;
        $uuid = $action_node->getActionClass()::getClassUuid();
        $api_call_type = ElementType::getElementType(uuid: $uuid);
        $node->api_or_action_type_id = $api_call_type->id;
        $node->thing_phase_id = $parent_thing->thing_phase_id;
        $node->thing_child_logic = $action_node->getActionChildLogic();
        $node->thing_logic = $action_node->getActionLogic();
        $node->thing_merge_method = $action_node->getMergeMethod();
        $node->save();
        /** @var \App\Api\Cmd\IActionParams $params */
        $params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,$uuid);
        $params->fromCollection($action_node->getCollection());
        $params->setupDataWithThing($node);


        foreach ($action_node->getActionChildren() as $action) {
            static::makeAction($node,$action);
        }
        return $node;

    }


    public static function getThing(
        ?int $id = null,
        ?int $rule_id = null,
        ?int $server_event_id = null,
    )
    : Thing
    {
        $ret = static::buildThing(id:$id,rule_id: $rule_id,server_event_id: $server_event_id)->first();

        if (!$ret) {
            $arg_types = [];
            $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($rule_id) { $arg_types[] = 'rule'; $arg_vals[] = $rule_id;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new \LogicException("Could not find thing via $arg_type : $arg_val");
        }
        return $ret;
    }

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

        /**
         * @uses Thing::thing_result(),Thing::thing_parent(),Thing::thing_children()
         */
        $build->with('thing_result','thing_parent','thing_children');

        return $build;
    }

    public function getName() : string {
        return "Hook # $this->id";
    }

}
