<?php

namespace App\Models;


use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int exposed_type_id
 * @property int exposed_attribute_id
 * @property int inherits_exposed_id
 * @property int exposed_parent_attribute_id
 * @property int exposed_parent_type_id

 *
 * @property Attribute exposed_attribute
 * @property ElementType exposed_type
 * @property Attribute exposed_parent_attribute
 * @property ElementType exposed_parent_type
 * @property ElementTypeExposedAttribute exposed_inheritance
 *
 * @property string created_at
 * @property string updated_at
 *
 */
class ElementTypeExposedAttribute extends Model
{

    protected $table = 'element_type_exposed_attributes';
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


    public function exposed_attribute() : BelongsTo {
        return $this->belongsTo(Attribute::class,'exposed_attribute_id');
    }

    public function exposed_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'exposed_attribute_id');
    }

    /** @noinspection PhpUnused */
    public function exposed_parent_attribute() : BelongsTo {
        return $this->belongsTo(Attribute::class,'exposed_attribute_id');
    }

    /** @noinspection PhpUnused */
    public function exposed_parent_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'exposed_attribute_id');
    }

    public function exposed_inheritance() : BelongsTo {
        return $this->belongsTo(static::class,'exposed_attribute_id');
    }

    /**
     * Clears out records from before for this type and then gets all the exposed attributes
     * By getting all the attributes for each ancestor, and removing each parent attribute as found
     *
     * @return ElementTypeExposedAttribute[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function makeRecords(ElementType $type)  {

        static::buildExposed(exposed_type_id: $type->id)->delete();
        $atts  = $type->getAllAttributes();
        $all_attributes = [];

        $parent_uuids_hash = [];
        foreach ($atts as $att) {
            $all_attributes[$att->getUuid()] = $att ;
            if($att->attribute_parent?->getUuid()) {
                $parent_uuids_hash[$att->attribute_parent?->getUuid()] = true;
            }

        }


        $exposed_uuids = [];
        $parent_uuids = array_keys($parent_uuids_hash);
        foreach ($all_attributes as $att_uuid => $att ) {

            if (in_array($att_uuid,$parent_uuids)) {
                continue;
            }
            $exposed_uuids[] = $att->getUuid();
        }

        $inserts = [];
        foreach ($exposed_uuids as  $exposed_uuid ) {
            $att = $all_attributes[$exposed_uuid];
            $inserts[] = [
                'exposed_type_id'=>$att->owner_element_type_id,
                'exposed_attribute_id'=>$att->id,
                'exposed_parent_attribute_id'=>($all_attributes[$att->attribute_parent?->getUuid()]??null)?->id,
                'exposed_parent_type_id'=>($all_attributes[$att->attribute_parent?->getUuid()]??null)?->owner_element_type_id,
                'inherits_exposed_id'=>null
            ];
        }



        ElementTypeExposedAttribute::insert($inserts);

        $recs = ElementTypeExposedAttribute::where('exposed_type_id',$type->id)->get();


        /**
         * @var static $row
         */
        foreach ($recs as $row) {
            if (!$row->exposed_parent_attribute_id) {continue;}
            $parent_row = ElementTypeExposedAttribute::where('exposed_type_id',$row->exposed_parent_type_id)
                ->where('exposed_attribute_id',$row->exposed_parent_attribute_id)->first();
            if ($parent_row) {
                $row->inherits_exposed_id = $parent_row->id;
                $row->save();
            }
        }

        /** @type  ElementTypeExposedAttribute[]|\Illuminate\Database\Eloquent\Collection */
        return static::buildExposed(exposed_type_id: $type->id)->get();

    }

    public static function getExposedAndVisible(
        int $exposed_type_id ,int $exposed_attribute_id , int $in_set_member_id
    )
    : static|null
    {
        /** @var static|null $ret */
        return static::buildExposed(exposed_type_id: $exposed_type_id,exposed_attribute_id: $exposed_attribute_id, in_set_member_id: $in_set_member_id)
            ->first();

    }



    public static function buildExposed(?int $exposed_type_id = null,?int $exposed_attribute_id = null,
                                        ?int $in_set_member_id = null,
                                        bool $with_exposed_attribute = false,bool $with_exposed_type = false,
                                        bool $with_exposed_inheritance = false
    )
    : Builder
    {
        /** @var Builder $build */
        $build = ElementTypeExposedAttribute::select('element_type_exposed_attributes.*')
            ->selectRaw(" extract(epoch from  element_type_exposed_attributes.created_at) as created_at_ts")
            ->selectRaw("extract(epoch from  element_type_exposed_attributes.updated_at) as updated_at_ts");


        if ($exposed_type_id) {
            $build->where('element_type_exposed_attributes.exposed_type_id',$exposed_type_id);
        }

        if ($exposed_attribute_id) {
            $build->where('element_type_exposed_attributes.exposed_attribute_id',$exposed_attribute_id);
        }

        if ($in_set_member_id && $exposed_type_id) {
            ElementTypeSetVisibility::buildVisibles(visible_type_id: $exposed_type_id, visible_set_member_id: $in_set_member_id,
                use_builder: $build, must_be_visible_in_scope: true);
        }

        if ($with_exposed_attribute) {
            $build/** @uses static::exposed_attribute() */ ->with('exposed_attribute');
        }

        if ($with_exposed_type) {
            $build/** @uses static::exposed_type() */ ->with('exposed_type');
        }

        if ($with_exposed_inheritance) {
            $build/** @uses static::exposed_inheritance() */ ->with('exposed_inheritance');
        }

        return $build;
    }

}
