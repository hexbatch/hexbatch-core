<?php

namespace App\Models;



use App\Enums\Types\TypeOfServerEventAccess;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


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

    public static function getEvent(
        ?int $id = null,
        ?int $owning_attribute_id = null,
        ?int $event_type_id = null,
        ?int $event_server_id = null,
    ) : ServerEvent {
        $ret = static::buildEvent(id:$id,owning_attribute_id:$owning_attribute_id, event_type_id: $event_type_id, event_server_id: $event_server_id)->first();

        if (!$ret) {
            $arg_types = [];
            $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($owning_attribute_id) { $arg_types[] = 'attribute'; $arg_vals[] = $owning_attribute_id;}
            if ($event_type_id) { $arg_types[] = 'type'; $arg_vals[] = $event_type_id;}
            if ($event_server_id) { $arg_types[] = 'server'; $arg_vals[] = $event_server_id;}

            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new \LogicException("Could not find server event via $arg_type : $arg_val");
        }
        return $ret;
    }

    public static function buildEvent(
        ?int $id = null,
        ?int $owning_attribute_id = null,
        ?int $event_type_id = null,
        ?int $event_server_id = null,
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

        if ($owning_attribute_id) {
            $build->where('server_events.owning_attribute_id',$owning_attribute_id);
        }

        if ($event_type_id) {
            $build->where('server_events.event_trigger_type_id',$event_type_id);
        }

        if ($event_server_id) {
            $build->where('server_events.event_server_id',$event_server_id);
        }






        return $build;
    }




    public function isInUse() : bool {

                //if any rule children have not been processed

        return false;
    }

}
