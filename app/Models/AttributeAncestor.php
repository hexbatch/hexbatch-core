<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int child_attribute_id
 * @property int ancestor_attribute_id
 * @property int attribute_gap
 *

 */
class AttributeAncestor extends Model
{

    protected $table = 'attribute_ancestors';
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
        foreach ($type->type_attributes as $att) {
            if (!$att->parent_attribute_id) { continue; }
            static::makeRecordsForAttribute($att);
        }
    }

    public static function makeRecordsForAttribute(Attribute $att) {
        $att_id = $att->id;

        $sql = "DELETE FROM attribute_ancestors a WHERE a.child_attribute_id = $att_id;";
        DB::statement($sql);

        $sql = "
         INSERT INTO attribute_ancestors(child_attribute_id,ancestor_attribute_id,attribute_gap )

                        with recursive attr_descendants as
                           (
                               (
                                   select attr_a.id as child_attribute_id,
                                          attr_a.parent_attribute_id as ancestor_attribute_id,
                                          1 as attribute_gap
                                   from attributes as attr_a where attr_a.id = $att_id
                                   and attr_a.attribute_approval = 'publishing_approved'

                               )
                               union all
                               (
                                   select attr_descendants.child_attribute_id as child_attribute_id ,
                                          attr_b.parent_attribute_id as ancestor_attribute_id,
                                          attribute_gap + 1 as attribute_gap


                                   from attributes as attr_b
                                            inner join attr_descendants on attr_descendants.ancestor_attribute_id = attr_b.id
                                   where attr_b.parent_attribute_id is NOT NULL
                                   and attr_b.attribute_approval = 'publishing_approved'
                               )
                           )

                        select distinct attr_descendants.child_attribute_id,
                                        attr_descendants.ancestor_attribute_id,
                                        attr_descendants.attribute_gap
                        from  attr_descendants

                        order by attribute_gap

                    ON CONFLICT DO NOTHING;
        ";

        DB::statement($sql);
    }

}
