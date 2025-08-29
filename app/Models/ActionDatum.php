<?php

namespace App\Models;


use ArrayObject;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_data_id
 * @property int root_data_id
 * @property int data_type_owner_id
 * @property int data_namespace_owner_id
 * @property int data_attribute_id
 * @property int data_second_attribute_id
 * @property int data_third_attribute_id
 * @property int data_type_id
 * @property int data_second_type_id
 * @property int data_element_id
 * @property int data_second_element_id
 * @property int data_set_member_id
 * @property int data_set_id
 * @property int data_second_set_id
 * @property int data_namespace_id
 * @property int data_path_id
 * @property int data_phase_id
 * @property int data_link_id
 * @property int data_second_phase_id
 * @property int data_user_id
 * @property int data_server_id
 * @property int data_mutual_id
 * @property bool is_system_privilege
 * @property bool is_sending_events
 * @property bool is_async
 * @property int action_wait_timeout_seconds
 * @property string ref_uuid
 * @property TypeOfThingStatus action_status
 * @property ArrayObject collection_data
 * @property ArrayObject data_tags
 * @property ElementType data_owner_type
 * @property UserNamespace data_owner_namespace
 * @property ActionDatum|null data_root
 * @property ActionDatum|null data_parent
 * @property ActionDatum[] data_children
 * @property ActionCollection[] action_collection
 * @property User data_user
 * @property UserNamespace data_namespace
 * @property ElementType data_type
 * @property ElementType data_second_type
 * @property Attribute data_attribute
 * @property Attribute data_second_attribute
 * @property Attribute data_third_attribute
 * @property Phase data_phase
 * @property Phase data_second_phase
 * @property ElementSet data_set
 * @property ElementSet data_second_set
 * @property Element data_element
 * @property Element data_second_element
 * @property Server data_server
 *
 */
class ActionDatum extends Model
{

    protected $table = 'action_data';
    public $timestamps = false;

    public function data_children() : HasMany {
        return $this->hasMany(ActionDatum::class,'parent_data_id','id');
    }


    public function action_collection() : HasMany {
        return $this->hasMany(ActionCollection::class,'parent_action_data_id','id');
    }

    public function data_parent() : BelongsTo {
        return $this->belongsTo(ActionDatum::class,'parent_data_id','id');
    }

    public function data_root() : BelongsTo {
        return $this->belongsTo(ActionDatum::class,'root_data_id','id');
    }

    public function data_owner_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'data_type_owner_id','id');
    }


    public function data_user() : BelongsTo {
        return $this->belongsTo(User::class,'data_user_id','id');
    }

    public function data_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'data_type_id','id');
    }

    public function data_second_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'data_second_type_id','id');
    }

    public function data_attribute() : BelongsTo {
        return $this->belongsTo(Attribute::class,'data_attribute_id','id');
    }

    public function data_second_attribute() : BelongsTo {
        return $this->belongsTo(Attribute::class,'data_second_attribute_id','id');
    }

    public function data_third_attribute() : BelongsTo {
        return $this->belongsTo(Attribute::class,'data_third_attribute_id','id');
    }

    public function data_namespace() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'data_namespace_id','id');
    }

    public function data_owner_namespace() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'data_namespace_owner_id','id');
    }

    public function data_phase() : BelongsTo {
        return $this->belongsTo(Phase::class,'data_phase_id','id');
    }

    public function data_link() : BelongsTo {
        return $this->belongsTo(ElementLink::class,'data_link_id','id');
    }

    public function data_second_phase() : BelongsTo {
        return $this->belongsTo(Phase::class,'data_second_phase_id','id');
    }

    public function data_element() : BelongsTo {
        return $this->belongsTo(Element::class,'data_element_id','id');
    }

    public function data_second_element() : BelongsTo {
        return $this->belongsTo(Element::class,'data_second_element_id','id');
    }

    public function data_set() : BelongsTo {
        return $this->belongsTo(ElementSet::class,'data_set_id','id');
    }

    public function data_second_set() : BelongsTo {
        return $this->belongsTo(ElementSet::class,'data_second_set_id','id');
    }

    public function data_server() : BelongsTo {
        return $this->belongsTo(Server::class,'data_server_id','id');
    }

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
        'is_sending_events' => 'boolean',
        'is_system_privilege' => 'boolean',
        'action_wait_timeout_seconds' => 'integer',
        'collection_data' => AsArrayObject::class,
        'data_tags' => AsArrayObject::class,
        'action_status' => TypeOfThingStatus::class,
    ];




    public static function buildHexbatchData(
        ?int $me_id = null,
        ?string $uuid = null,
        ?int $data_action_type_id = null,
        ?int $collection_namespace_id = null,
        bool $b_linkages = false
    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build =  ActionDatum::select('action_data.*')

        ;

        if ($me_id) {
            $build->where('action_data.id',$me_id);
        }

        if ($uuid) {
            $build->where('action_data.ref_uuid',$uuid);
        }

        if ($data_action_type_id) {
            $build->where('action_data.data_type_id',$data_action_type_id);
        }

        if ($collection_namespace_id) {
            $build->where('action_data.data_namespace_id',$collection_namespace_id);
        }

        if ($b_linkages) {
            /** @uses static::data_children(),static::data_root(),static::data_parent(),static::data_owner_type(),static::action_collection() */
            $build->with('data_children','data_root','data_parent','data_owner_type','action_collection');
        }

        return $build;
    }

    public function addUuidsToCollection(string $class, array $uuids, ?int $partition_flag = 0, bool $b_check_and_throw = false) :void  {
        if (empty($uuids)) {return;}
        ActionCollection::addUuids(parent: $this, class: $class, uuids: $uuids, partition_flag: $partition_flag, b_check_and_throw: $b_check_and_throw);
    }

    /** @return Attribute[]|ElementType[]|ElementSet[]|Element[]|ElementSetMember[]|UserNamespace[]|Path[]|User[]|Server[]|Mutual[] */
    public function getCollectionOfType(string $class, ?int $partition_flag = null) :array  {
        $ret = [];
        $col = ActionCollection::CLASS_TO_BELONGS_MAPPING[$class]??null;
        if (!$col) { throw new \LogicException("[getCollectionOfType] unknown class in mapping: $class");}
        foreach ($this->action_collection as $collect) {
            if ($collect->$col) {
                if ($partition_flag !== null) {
                    if ($collect->collection_partition_flag !== $partition_flag) {
                        continue;
                    }
                }

                $ret[] = $collect->$col;
            }
        }

        //filter duplicate objects, do not use distinct in the action_collection because we may want duplicates later
        $collection = array_filter($ret, function($obj)
        {
            static $idList = array();
            if(in_array($obj->id,$idList)) {
                return false;
            }
            $idList []= $obj->id;
            return true;
        });

        return $collection;
    }
    /** @return string[] */
    public function getUuidsFromCollection(string $class, ?int $partition_flag = 0) :array  {
        $ret = [];
        $mine = $this->getCollectionOfType(class: $class,partition_flag: $partition_flag);
        foreach ($mine as $what) {
            $ret[] = $what->ref_uuid;
        }

        return $ret;
    }

}
