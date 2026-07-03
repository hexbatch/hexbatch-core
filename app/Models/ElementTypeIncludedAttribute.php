<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int included_type_id
 * @property int included_attribute_id

 *
 * @property Attribute included_attribute
 * @property ElementType included_type

 *
 * @property string created_at
 * @property string updated_at
 *
 */
class ElementTypeIncludedAttribute extends Model
{

    protected $table = 'element_type_included_attributes';
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

    ];


    public function included_attribute() : BelongsTo {
        return $this->belongsTo(Attribute::class,'included_attribute_id');
    }

    public function included_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'included_type_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection<int,static>
     */
    public static function getAllAttributes(array $type_ids, array $attribute_ids = [],bool $b_with_type = false) {
        $builder =  static::buildIncluded(included_type_ids: $type_ids,included_attribute_ids: $attribute_ids,
            with_included_attribute: true,with_included_type: $b_with_type);

        return $builder->orderByRaw('included_type_id, included_attribute_id')
            ->get();
    }

    /**
     * @return int[]
     */
    public static function filterTypeIdsByAttributes( array $type_ids,array $attribute_ids ) {
        $found_type_ids =  static::buildIncluded(included_type_ids: $type_ids,included_attribute_ids: $attribute_ids)->pluck('included_type_id')->toArray();
        return array_intersect($type_ids,$found_type_ids);
    }


    /**
     * Clears out records from before for this type and then gets all the included attributes
     *
     */
    public static function makeRecords(?int $type_id = null) :void {



        static::buildIncluded(included_type_ids: [$type_id])->delete();


         DB::affectingStatement(
             "
             insert into element_type_included_attributes(included_type_id,included_attribute_id)
                (
                    WITH all_subtypes as
                              (SELECT t.ancestor_type_id as type_id
                               from element_type_ancestors t
                               where t.owning_child_type_id = :type_id
                               UNION
                               DISTINCT
                               select :type_id as type_id),

                          top_attributes as
                              (SELECT top_att.id as top_att_id
                               FROM attributes top_att
                                        INNER JOIN all_subtypes
                                                   ON all_subtypes.type_id = top_att.owner_element_type_id),

                          inherited_attributes as
                              (SELECT an.id as anc_att_id
                               from attribute_ancestors an
                                        INNER JOIN top_attributes ON top_attributes.top_att_id = an.child_attribute_id),

                          all_attributes as
                              (select top_attributes.top_att_id as att_id
                               from top_attributes
                               UNION
                               DISTINCT
                               select inherited_attributes.anc_att_id as att_id
                               from inherited_attributes)

                     SELECT :type_id as included_type_id, a.att_id as included_attribute_id
                     FROM all_attributes a
                              INNER JOIN attributes wa on wa.id = a.att_id
                              INNER JOIN element_types ty on ty.id = wa.owner_element_type_id
                 )
                ;
                   "
                 ,['type_id'=>$type_id]);



    }





    public static function buildIncluded(array $included_type_ids = [], array $included_attribute_ids = [],
                                         bool $with_included_attribute = false, bool $with_included_type = false,
                                         bool $using_select = true
    )
    : Builder
    {
        /** @var Builder $build */
        $build = ElementTypeIncludedAttribute::where('id','>',0);

        if ($using_select)
        {
            $build->select('element_type_included_attributes.*')
                ->selectRaw(" extract(epoch from  element_type_included_attributes.created_at) as created_at_ts")
                ->selectRaw("extract(epoch from  element_type_included_attributes.updated_at) as updated_at_ts");
        }



        if (count($included_type_ids)) {
            $build->whereIn('element_type_included_attributes.included_type_id',$included_type_ids);
        }

        if (count($included_attribute_ids)) {
            $build->whereIn('element_type_included_attributes.included_attribute_id',$included_attribute_ids);
        }


        if ($with_included_attribute) {
            $build/** @uses static::included_attribute() */ ->with('included_attribute');
        }

        if ($with_included_type) {
            $build/** @uses static::included_type() */ ->with('included_type');
        }


        return $build;
    }

}
