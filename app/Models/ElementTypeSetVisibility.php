<?php

namespace App\Models;



use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;


/**
 * An element can go into a set if its overall bounds fits (if it has bounds)
 * but it can have subtypes which have bounds that do not intersect, if that is true,
 * then that subtypes' values is turned off and cannot be on in the set or down-set
 *
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int visible_type_id
 * @property int visible_set_member_id
 * @property bool is_visible (calculated field cannot change)
 * @property bool is_visible_for_location
 * @property bool is_visible_for_schedule
 * @property bool is_turned_on
 *
 *
 */
class ElementTypeSetVisibility extends Model
{

    protected $table = 'element_type_set_visibilities';
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
    protected $casts = [];

    public static function stateVisibility(int $visible_type_id,?int $visible_set_member_id,
        ?bool $is_visible_for_location = null, ?bool $is_visible_for_schedule = null,
        ?int $phase_id = null, ?bool $is_turned_on = null
    )
    :void
    {

        $starting = [
            'visible_type_id' => $visible_type_id,
        ];
        $ending = [];
        $unique_by = [ 'visible_type_id' ];

        if ($visible_set_member_id !== null) {
            $starting['visible_set_member_id'] = $visible_set_member_id;
            $unique_by[] = 'visible_set_member_id';
        }

        if ($is_visible_for_location !== null) {
            $starting['is_visible_for_location'] = $is_visible_for_location;
            $ending['is_visible_for_location'] = $is_visible_for_location;
        }

        if ($is_visible_for_schedule !== null) {
            $starting['is_visible_for_schedule'] = $is_visible_for_schedule;
            $ending['is_visible_for_schedule'] = $is_visible_for_schedule;
        }

        if ($is_turned_on !== null) {
            $starting['is_turned_on'] = $is_turned_on;
            $ending['is_turned_on'] = $is_turned_on;
        }

        if ($phase_id) {
            static::join('element_set_members as mems','mems.id','=','element_type_set_visibilities.visible_set_member_id');
            static::join('elements as els',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use ($phase_id) {
                    $join
                        ->on('els.id', '=', 'mems.member_element_id')
                        ->where('els.element_phase_id', $phase_id);
                }
            )::upsert($starting, $unique_by,$ending);
        } else {
            static::upsert($starting, $unique_by,$ending);
        }

    }

    public static function buildVisibles(?int     $visible_type_id = null,
                                         ?int     $visible_set_member_id = null,
                                         ?int     $phase_id = null,
                                         ?Builder $use_builder = null,
                                         bool     $must_be_visible_in_scope = false

    )
    : Builder
    {
        if ($use_builder) {
            $build = $use_builder;
        } else {
            /** @var Builder $build */
            $build = ElementTypeSetVisibility::select('element_type_set_visibilities.*')
                ->selectRaw(" extract(epoch from  element_type_set_visibilities.created_at) as created_at_ts")
                ->selectRaw("extract(epoch from  element_type_set_visibilities.updated_at) as updated_at_ts");
        }



        if ($visible_type_id) {
            $build->where('element_type_set_visibilities.visible_type_id',$visible_type_id);
        }

        if ($visible_set_member_id) {
            $build->where('element_type_set_visibilities.visibility_set_id',$visible_set_member_id);
        }

        if ($phase_id) {
            $build->join('element_set_members as mems','mems.id','=','element_type_set_visibilities.visible_set_member_id');
            $build->join('elements as els',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use ($phase_id) {
                    $join
                        ->on('els.id', '=', 'mems.member_element_id')
                        ->where('els.element_phase_id', $phase_id);
                }
            );
        }

        if ($must_be_visible_in_scope) {
            Utilities::ignoreVar($must_be_visible_in_scope);
            //todo cte: needs to not exist in element_type_set_visibilities for the set,
            // or is_visible = true,  for set and all parents including set-less : if not any of that, then query does not find anything
        }


        return $build;
    }
}
