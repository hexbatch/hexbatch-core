<?php

namespace App\Models;



use App\Enums\Things\TypeOfThingStatus;
use App\Enums\Types\TypeOfServerEventAccess;
use App\Exceptions\HexbatchCoreException;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


/*
 * cannot listen to a system type or system attribute
 *
 *   note: when propagating the type to another server: the reported events to send, and the denied events,
            are discovered by the server_events combined with the attribute access level with the entry for the server on the element_type_server_levels
	 This will be reported as type that has such an event, and either callable for that server or  that it exists but is forbidden
	 types from other servers that do not have a forbidden event, or a defined event, will not have an entry in the server_events
	 Example:
	    so if the type has a hook for element creation, but that or those attributes are not included in the elsewhere access level
                (the attr are protected and the server is public for example)


 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int event_trigger_type_id
 * @property int event_server_id
 * @property int owning_attribute_id
 * @property int event_target_type_id
 * @property int event_target_attribute_id
 * @property int blocked_by_server_event_id
 * @property int filtered_by_server_event_id
 * @property int source_live_attribute_id

 * @property bool is_listening_before
 * @property TypeOfServerEventAccess event_access
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property AttributeRule top_rule
 * @property Attribute event_owner
 * @property ElementType listening_to_event
 */
class ServerEvent extends Model
{

    protected $table = 'server_events';
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
        'event_access' => TypeOfServerEventAccess::class
    ];


    public function rule_root() : HasOne {
        return $this->hasOne(AttributeRule::class,'owning_server_event_id')->whereNull('parent_rule_id');
    }

    public function event_owner() : BelongsTo {
        return $this->belongsTo(Attribute::class,'owning_attribute_id');
    }

    public function listening_to_event() : BelongsTo {
        return $this->belongsTo(ElementType::class,'event_trigger_type_id');
    }


    public static function buildEvent(
        ?int $id = null,
        ?int $owning_attribute = null,
        ?int $event_type_id = null
    )
    : Builder
    {

        $build =  ServerEvent::select('server_events.*')
            ->selectRaw(" extract(epoch from  server_events.created_at) as created_at_ts,  extract(epoch from  server_events.updated_at) as updated_at_ts")
            /** @uses ServerEvent::rule_root(),ServerEvent::event_owner(),ServerEvent::listening_to_event() */
            ->with('rule_root', 'event_owner','listening_to_event')


        ;

        if ($id) {
            $build->where('server_events.id',$id);
        }

        if ($owning_attribute) {
            $build->where('server_events.owning_attribute_id',$owning_attribute);
        }

        if ($event_type_id) {
            $build->where('server_events.event_trigger_type_id',$event_type_id);
        }





        return $build;
    }


    public static function collectEvent(Collection|string $collect,?Attribute $owner = null,?ServerEvent $event = null ) : ServerEvent {



        try {
            DB::beginTransaction();
            if(!$event) {
                $event = new ServerEvent();

            }
            if ($owner) {
                $event->owning_attribute_id = $owner->id;
            }
            $event->editEvent($collect);

            DB::commit();

            return ServerEvent::buildEvent(id:$event->id)->first();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof HexbatchCoreException) {
                throw $e;
            }
            throw new HexbatchNotPossibleException(
                $e->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);

        }
    }


    /**
     * @throws \Exception
     */
    public function editEvent(Collection $collect) : void {

        if ($this->isInUse()) {
            throw new HexbatchNotPossibleException(
                __('msg.event_in_use'),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::RULE_CANNOT_EDIT);
        }
        try {
            DB::beginTransaction();



            if ($collect->has('rules')) {
                collect($collect->get('rules'))->each(function ($hint_child, int $key) {
                    Utilities::ignoreVar($key);
                    AttributeRule::collectRule(collect: $hint_child,owner_event: $this);
                });
            }

            if ($collect->has('event')) {
                $hint_event = $collect->get('event');
                if (is_string($hint_event) && Utilities::is_uuid($hint_event)) {
                    /**
                     * @var ElementType $event_type
                     */
                    $event_type = (new ElementType())->resolveRouteBinding($hint_event);
                    //todo see if the event type is a valid event
                    $this->event_trigger_type_id = $event_type->id;
                }
            }

            if ($collect->has('target_type')) {
                $hint_target = $collect->get('target_type');
                if (is_string($hint_target) && Utilities::is_uuid($hint_target)) {
                    /**
                     * @var ElementType $target_type
                     */
                    $target_type = (new ElementType())->resolveRouteBinding($hint_target);
                    $this->event_target_type_id = $target_type->id;
                }
            }

            if ($collect->has('target_attribute')) {
                $hint_attr = $collect->get('target_attribute');
                if (is_string($hint_attr) && Utilities::is_uuid($hint_attr)) {
                    /**
                     * @var Attribute $event_type
                     */
                    $event_type = (new Attribute())->resolveRouteBinding($hint_attr);
                    $this->event_target_attribute_id = $event_type->id;
                }
            }


            if ($collect->has('is_listening_before')) {
                $this->is_listening_before = Utilities::boolishToBool($collect->get('is_listening_before',true));
            }



            try {
                $this->save();
            } catch (\Exception $f) {
                throw new HexbatchNotPossibleException(
                    __('msg.event_cannot_be_edited',['error'=>$f->getMessage()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::RULE_SCHEMA_ISSUE);
            }


            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function isInUse() : bool {

        if (Thing::buildThing(server_event_id: $this->id)->where('thing_status',TypeOfThingStatus::THING_PENDING)->count() ) {return true;}
        //if any rule children have not been processed

        return false;
    }

}
