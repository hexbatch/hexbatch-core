<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int exposed_type_id
 * @property int exposed_attribute_id

 *
 * @property Attribute exposed_attribute
 * @property ElementType exposed_type

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
        return $this->belongsTo(ElementType::class,'exposed_type_id');
    }


    public static function findExposedAttributes(ElementType $type)
    : array
    {
        // get the terminal chains of inheritance (by finding the system id at the end of each array or not)
        // for all types get the attributes with parent lists
        //for each chain get all atts and  from the end, add to a new collection, add all atts from that type, go to the next type up
            // if type has att A or parent chain that uses any of the last level attribute B, then remove B from the collection. Add all A
        // union all chain collections at the end
        $attributes_used = new Collection;
        $chains = ElementTypeParent::getInheritanceChains($type);
        $all_type_ids_hash = [];
        foreach ($chains as $chain) {
            foreach ($chain as $some_id) {
                $all_type_ids_hash[$some_id] = $some_id;
            }
        }
        $all_type_ids = array_values($all_type_ids_hash);

        if (empty($all_type_ids)) {return [];}

        /** @var Collection<Attribute> $all_attributes */
        $all_attributes = Attribute::buildAttribute(in_type_ids: $all_type_ids,b_do_ancestors: true)->get();
        /** @var array<Attribute[]> $attr_type_hash */
        $attr_type_hash = [];
        foreach ($all_attributes as $att) {
            if (!isset($all_type_ids_hash[$att->owner_element_type_id])) {
                $attr_type_hash[$att->owner_element_type_id] = [];
            }
            $attr_type_hash[$att->owner_element_type_id][] = $att;
        }

        $collections = [];
        foreach ($chains as $chain) {
            /** @var Collection<Attribute> $chain_collection */
            $collections[] = $chain_collection = new Collection;
            foreach (array_reverse($chain) as $some_id) { //start with the root and work up

                foreach (($attr_type_hash[$some_id]??[]) as $att ) {
                    //remove older that are in inheritance chain of newer
                    foreach ($chain_collection as $what) {
                        foreach ($what->attribute_ancestors as $ancestor) {
                            if ($ancestor->id === $what->id) {
                                //what is in the ancestor chain of anc, so remove what
                                $chain_collection->forget([$what->ref_uuid]);
                            }
                        }

                    }
                    if (!$chain_collection->has($att->ref_uuid)) {
                        $chain_collection->offsetSet($att->ref_uuid,$att);
                    }

                }
            }
        }
        foreach ($collections as $type_collection) {
            $attributes_used =  $attributes_used->merge($type_collection);
        }
        $unique_collection = $attributes_used->unique('ref_uuid');
        $ret = [];
        foreach ($unique_collection as $att) {
            $ret[] = $att;
        }
        return $ret;
    }





    /**
     * Clears out records from before for this type and then gets all the exposed attributes
     * By getting all the attributes for each ancestor, and removing each parent attribute as found
     *
     * @return ElementTypeExposedAttribute[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function makeRecords(ElementType $type)  {

        static::buildExposed(exposed_type_id: $type->id)->delete();
        $atts  = static::findExposedAttributes($type);

        $inserts = [];
        foreach ($atts as  $att ) {
            $inserts[] = [
                'exposed_type_id'=>$type->id,
                'exposed_attribute_id'=>$att->id,
                'exposed_parent_type_id'=>$att->owner_element_type_id
            ];
        }



        ElementTypeExposedAttribute::insert($inserts);

        /** @type  ElementTypeExposedAttribute[]|\Illuminate\Database\Eloquent\Collection */
        return static::buildExposed(exposed_type_id: $type->id)->get();

    }

    public static function getExposedAndVisible(
        int $exposed_type_id ,int $exposed_attribute_id , int $in_set_member_id, ?int $phase_id = null
    )
    : static|null
    {
        /** @var static|null $ret */
        return static::buildExposed(exposed_type_id: $exposed_type_id,exposed_attribute_id: $exposed_attribute_id,
            in_set_member_id: $in_set_member_id,phase_id: $phase_id)
            ->first();

    }



    public static function buildExposed(?int $exposed_type_id = null,?int $exposed_attribute_id = null,
                                        ?int $in_set_member_id = null,
                                        ?int $phase_id = null,
                                        bool $with_exposed_attribute = false,bool $with_exposed_type = false
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
            ElementVisibility::buildVisibles(visible_type_id: $exposed_type_id, visible_set_member_id: $in_set_member_id,phase_id: $phase_id,
                use_builder: $build, must_be_visible_in_scope: true);
        }

        if ($with_exposed_attribute) {
            $build/** @uses static::exposed_attribute() */ ->with('exposed_attribute');
        }

        if ($with_exposed_type) {
            $build/** @uses static::exposed_type() */ ->with('exposed_type');
        }


        return $build;
    }

}
