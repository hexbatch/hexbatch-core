<?php

namespace App\Models;


use App\Enums\Types\TypeOfApproval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owning_child_type_id
 * @property int ancestor_type_id
 * @property int type_gap
 *

 */
class ElementTypeAncestor extends Model
{

    protected $table = 'element_type_ancestors';
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

    public static function makeRecordsForType(ElementType $type) {
        $type_id = $type->id;

        $sql = "DELETE FROM element_type_ancestors a WHERE a.owning_child_type_id = $type_id";
        DB::statement($sql);

        $b_stop = true;

        foreach ($type->type_parents as $par) {
            if ($par->parent_type_approval === TypeOfApproval::PUBLISHING_APPROVED) {
                $b_stop = false;
                break;
            }
        }
        if ($b_stop) {return;}


        $sql = "
                INSERT INTO element_type_ancestors(owning_child_type_id,ancestor_type_id,type_gap )

                    with recursive type_descendants as
                                       (
                                           (
                                               select par_a.child_type_id as owning_child_type_id,
                                                      par_a.child_type_id as current_id,
                                                      par_a.parent_type_id as ancestor_type_id,
                                                      1 as type_gap
                                               from element_type_parents as par_a where par_a.child_type_id = $type_id
                                                   AND  par_a.parent_type_approval  IN ('publishing_approved')
                                           )
                                           union all
                                           (
                                               select type_descendants.owning_child_type_id as owning_child_type_id ,
                                                      par_b.child_type_id as current_id,
                                                      par_b.parent_type_id                  as ancestor_type_id,
                                                      type_gap + 1                          as type_gap


                                               from element_type_parents as par_b
                                                  inner join type_descendants on type_descendants.ancestor_type_id = par_b.child_type_id
                                               where par_b.parent_type_approval  IN ('publishing_approved')
                                           )
                                       )

                    select distinct

                                    type_descendants.owning_child_type_id,
                                    type_descendants.ancestor_type_id,
                                    type_descendants.type_gap
                    from  type_descendants

                    order by type_gap

                ON CONFLICT DO NOTHING;
        ";

        DB::statement($sql);
    }

}
