<?php

namespace App\Models;


use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_action_data_id
 * @property int collection_attribute_id
 * @property int collection_type_id
 * @property int collection_element_id
 * @property int collection_set_member_id
 * @property int collection_set_id
 * @property int collection_namespace_id
 * @property int collection_path_id
 * @property int collection_user_id
 * @property int collection_server_id
 * @property int collection_mutual_id
 * @property int collection_phase_id
 *
 * @property int collection_partition_flag
 * @property ActionDatum|null collection_parent
 * @property User collection_user
 * @property UserNamespace collection_namespace
 * @property ElementType collection_type
 * @property Attribute collection_attribute
 * @property ElementSet collection_set
 * @property ElementSetMember collection_set_member
 * @property Element collection_element
 * @property Path collection_path
 * @property Server collection_server
 * @property Mutual collection_mutual
 *
 */
class ActionCollection extends Model
{

    protected $table = 'action_collections';
    public $timestamps = false;




    public function collection_parent() : BelongsTo {
        return $this->belongsTo(ActionCollection::class,'parent_action_data_id','id');
    }


    const array CLASS_TO_BELONGS_MAPPING = [
        /** @uses static::collection_attribute() */
        Attribute::class => 'collection_attribute',

        /** @uses static::collection_type() */
        ElementType::class => 'collection_type',

        /** @uses static::collection_set() */
        ElementSet::class => 'collection_set',

        /** @uses static::collection_element() */
        Element::class => 'collection_element',

        /** @uses static::collection_set_member() */
        ElementSetMember::class => 'collection_set_member',

        /** @uses static::collection_namespace() */
        UserNamespace::class => 'collection_namespace',

        /** @uses static::collection_path() */
        Path::class => 'collection_path',

        /** @uses static::collection_user() */
        User::class => 'collection_user',

        /** @uses static::collection_server() */
        Server::class => 'collection_server',

        /** @uses static::collection_mutual() */
        Mutual::class => 'collection_mutual',

        /** @uses static::collection_phase() */
        Phase::class => 'collection_phase',
    ];

    public function collection_user() : BelongsTo {
        return $this->belongsTo(User::class,'collection_user_id','id');
    }

    public function collection_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'collection_type_id','id');
    }

    public function collection_attribute() : BelongsTo {
        return $this->belongsTo(Attribute::class,'collection_attribute_id','id');
    }

    public function collection_namespace() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'collection_namespace_id','id');
    }

    public function collection_set() : BelongsTo {
        return $this->belongsTo(ElementSet::class,'collection_set_id','id');
    }

    public function collection_set_member() : BelongsTo {
        return $this->belongsTo(ElementSetMember::class,'collection_set_member_id','id');
    }

    public function collection_element() : BelongsTo {
        return $this->belongsTo(Element::class,'collection_element_id','id');
    }

    public function collection_path() : BelongsTo {
        return $this->belongsTo(Path::class,'collection_path_id','id');
    }

    public function collection_server() : BelongsTo {
        return $this->belongsTo(Server::class,'collection_server_id','id');
    }

    public function collection_mutual() : BelongsTo {
        return $this->belongsTo(Mutual::class,'collection_mutual_id','id');
    }

    public function collection_phase() : BelongsTo {
        return $this->belongsTo(Phase::class,'collection_phase_id','id');
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
        'collection_partition_flag'=>'integer'
    ];


    /** @noinspection PhpUnused */
    public static function buildActionCollection(
        ?int $me_id = null,
        ?int $parent_action_data_id = null,
        bool $b_linkages = false
    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build =  ActionCollection::select('action_collections.*');

        if ($me_id) {
            $build->where('action_collections.id',$me_id);
        }

        if ($parent_action_data_id) {
            $build->where('action_collections.parent_action_data_id',$parent_action_data_id);
        }


        if ($b_linkages) {
            /** @uses static::collection_parent() */
            $build->with('collection_parent');
        }

        return $build;
    }

    const array CLASS_TO_COLUMN_MAPPING = [
        Attribute::class => 'collection_attribute_id',
        ElementType::class => 'collection_type_id',
        ElementSet::class => 'collection_set_id',
        Element::class => 'collection_element_id',
        ElementSetMember::class => 'collection_set_member_id',
        UserNamespace::class => 'collection_namespace_id',
        Path::class => 'collection_path_id',
        User::class => 'collection_user_id',
        Server::class => 'collection_server_id',
        Mutual::class => 'collection_mutual_id',
        Phase::class => 'collection_phase',
    ];

    public static function addUuids(ActionDatum $parent,string $class,array $uuids, int $partition_flag = 0,bool $b_check_and_throw = false)
    : void
    {

        $uuids = array_unique($uuids);
        $column = static::CLASS_TO_COLUMN_MAPPING[$class]??null;
        if (!$column) {
            throw new \LogicException('[addUuids] Unexpected class '.$class);
        }
        Utilities::is_uuid_array($uuids,true);

        $table_name = app($class)->getTable();

        $what_laravel = DB::table("$table_name as my_tab")
            ->selectRaw('my_tab.id, my_tab.ref_uuid, act.id as collect_id')
            ->whereIn('my_tab.ref_uuid',$uuids)
            ->leftJoin("action_collections as act",
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($parent,$column) {
                    $join
                        ->on("act.$column",'=',"my_tab.id")
                        ->where('act.parent_action_data_id',$parent->id);
                }
            )
            ;

        $what = $what_laravel->get()->toArray();

        $ref = [];
        $todo = [];
        foreach ($what as $row) {
            $ref[$row->ref_uuid] = $row->id;
            if (!$row->collect_id) {
                $todo[$row->ref_uuid] = $row->id;
            }
        }

        $missing = [];
        foreach ($uuids as $uuid) {
            if (!isset($ref[$uuid])) {$missing[] = $uuid;}
        }

        if ($b_check_and_throw) {
            if (count($missing) ) {
                throw new \InvalidArgumentException("Uuids not found in $table_name : ". implode('|',$missing));
            }
        }

        //see what is already here, and then add the others


        $inserts = [];
        foreach ($todo as $table_id ) {
            $inserts[] = ['parent_action_data_id'=>$parent->id,$column=>$table_id,'collection_partition_flag'=>$partition_flag];
        }
        ActionCollection::insert($inserts);

    }

}
