<?php

namespace App\Models;



use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;


/**
 * An element can go into a set if its overall bounds overlaps (if it has bounds)
 * but it can have subtypes which have bounds that do not intersect, if that is true,
 * then that subtypes' values is turned off and cannot be on in the set or down-set.
 * If a type has no location bounds, then its always visible for location
 *
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int visible_type_id
 * @property int visible_element_id
 * @property int visible_set_member_id
 * @property bool is_visible (calculated field cannot change)
 * @property bool is_visible_for_schedule
 * @property bool is_turned_on
 *
 *
 */
class ElementVisibility extends Model
{

    protected $table = 'element_visibilities';
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

    public static function switchVisibility(SelectElementParamData $params,
                                            ?bool                   $is_turned_on = null,
                                            ?bool                    $is_visible_for_schedule = null,

    )
    :void
    {
        if (is_null($is_turned_on) && is_null($is_visible_for_schedule)) {
            return; //nothing to do
        }

        $element_builder  = Element::buildElement(
            given_uuids: $params->element_refs, set_ref: $params->set_ref, phase_ref: $params->phase_ref,
            type_ref: $params->type_ref, namespace_ref: $params->namespace_ref, attribute_ref: $params->attribute_ref,
            b_use_select: false
        );


        $element_builder->selectRaw('elements.element_parent_type_id as visible_type_id');
        $element_builder->selectRaw('elements.id as visible_element_id');

        if ($params->set_ref) {
            $element_builder->selectRaw('semp.id as visible_set_member_id');
        } else {
            $element_builder->selectRaw('NULL as visible_set_member_id');
        }

        $is_visible_for_schedule_word = 'false';
        if ($is_visible_for_schedule || $is_visible_for_schedule === null) {
            $is_visible_for_schedule_word = 'true';
        }

        $is_turned_on_word = 'false';
        if ($is_turned_on || $is_turned_on === null) {
            $is_turned_on_word = 'true';
        }
        $element_builder->selectRaw("$is_visible_for_schedule_word as is_visible_for_schedule");
        $element_builder->selectRaw("$is_turned_on_word as is_turned_on");




        $element_sql = $element_builder->toRawSql();

        $updated_columns = [];
        if ($is_turned_on !== null ) {
            $updated_columns[] = "is_turned_on = $is_turned_on_word";
        }

        if ($is_visible_for_schedule !== null ) {
            $updated_columns[] = "is_visible_for_schedule = $is_visible_for_schedule_word";
        }

        $updated_columns_string = implode(', ',$updated_columns);

        $upsert_sql = "
          INSERT INTO element_visibilities (visible_type_id,visible_element_id,visible_set_member_id,is_visible_for_schedule,is_turned_on)
            ($element_sql)
          ON CONFLICT (visible_type_id,visible_element_id,visible_set_member_id)
          DO UPDATE SET $updated_columns_string
          ;
        ";

        DB::statement($upsert_sql);



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
            $build = ElementVisibility::select('element_visibilities.*')
                ->selectRaw(" extract(epoch from  element_visibilities.created_at) as created_at_ts")
                ->selectRaw("extract(epoch from  element_visibilities.updated_at) as updated_at_ts");
        }



        if ($visible_type_id) {
            $build->where('element_visibilities.visible_type_id',$visible_type_id);
        }

        if ($visible_set_member_id) {
            $build->where('element_visibilities.visibility_set_id',$visible_set_member_id);
        }

        if ($phase_id) {
            $build->join('element_set_members as mems','mems.id','=','element_visibilities.visible_set_member_id');
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
            //todo cte: needs to not exist in element_visibilities for the set,
            // or is_visible = true,  for set and all parents including set-less : if not any of that, then query does not find anything
        }


        return $build;
    }
}
