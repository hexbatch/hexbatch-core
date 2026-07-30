<?php

namespace App\Models;


use App\Enums\Types\TypeOfLiveRulePolicy;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Rules defined at type design time
 * Approved in publish
 * Applied in each set the type's element makes
 * toggled when trigger type enters or exits set
 *
 *
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int live_rule_owner_type_id
 * @property int live_rule_trigger_type_id
 * @property int live_rule_target_type_id
 * @property string ref_uuid
 * @property TypeOfLiveRulePolicy live_rule_policy
 *
 * @property string created_at
 * @property string updated_at
 *
 */
class LiveRule extends Model
{

    protected $table = 'live_rules';
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
        'live_rule_policy' => TypeOfLiveRulePolicy::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function live_rule_owner() : BelongsTo {
        return $this->belongsTo(ElementType::class,'live_rule_owner_type_id');
    }

    public function live_rule_trigger() : BelongsTo {
        return $this->belongsTo(ElementType::class,'live_rule_trigger_type_id');
    }

    public function type_live_target() : BelongsTo {
        return $this->belongsTo(ElementType::class,'live_rule_target_type_id');
    }

    public static function buildLiveRule(
    ?int      $me_id = null,
    ?string    $me_uuid = null,
    bool    $b_do_relations = false
)
: Builder
    {
        /** @var Builder $build */
        $build = LiveRule::select('live_rules.*')
            ->selectRaw(" extract(epoch from  live_rules.created_at) as created_at_ts")
            ->selectRaw("  extract(epoch from  live_rules.updated_at) as updated_at_ts");

        if ($b_do_relations) {
            /** @uses static::live_rule_owner(),static::live_rule_trigger(),static::type_live_target() */
            $build->with('live_rule_owner', 'live_rule_trigger', 'type_live_target');
        }


        if ($me_id) {
            $build->where('live_rules.id', $me_id);
        }

        if ($me_uuid) {
            $build->where('live_rules.ref_uuid', $me_uuid);
        }

        return $build;
    }


    public static function getThisLiveRule(
        ?int             $id = null,
        ?string          $uuid = null,
        bool        $b_do_relations = false
    )
    : Attribute
    {
        $ret = static::buildLiveRule(me_id:$id,me_uuid: $uuid,b_do_relations: $b_do_relations)->first();

        if (!$ret) {
            $arg_types = []; $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($uuid) { $arg_types[] = 'uuid'; $arg_vals[] = $uuid;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new HexbatchNotFound(
                __('msg.live_rule_not_found_by',['types'=>$arg_type,'values'=>$arg_val]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::LIVE_RULE_NOT_FOUND
            );
        }
        return $ret;
    }

}
