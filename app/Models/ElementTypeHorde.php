<?php

namespace App\Models;



use App\Enums\Types\TypeOfApproval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int horde_type_id
 * @property int horde_attribute_id
 * @property int originating_horde_id
 * @property TypeOfApproval attribute_approval
 *
 *
 * @property Attribute horde_attribute
 */
class ElementTypeHorde extends Model
{

    protected $table = 'element_type_hordes';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'horde_type_id',
        'horde_attribute_id',
    ];

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
        'attribute_approval' => TypeOfApproval::class
    ];

    public function horde_attribute() : BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'horde_attribute_id');
    }

    public static function addAttribute(Attribute $attribute,ElementType $type) :ElementTypeHorde {
        $horde = new ElementTypeHorde();

        $horde->upsert([
            'horde_attribute_id' => $attribute->id,
            'horde_type_id' => $type->id,
        ],['horde_type_id','horde_attribute_id']);
        $horde->refresh();
        return $horde;
    }



    public static function getDecendants(Attribute $attribute) : Builder {

        $attr_id = $attribute->id;
        $type_id = $attribute->owner_element_type_id;
        $res = DB::select(
            "
            WITH RECURSIVE rec (id) as
                   (SELECT attributes.id,
                           attributes.attribute_name,
                           attributes.parent_attribute_id
                    from attributes
                    where attributes.parent_attribute_id = $attr_id

                    UNION ALL

                    SELECT attributes.id,
                           attributes.attribute_name,
                           attributes.parent_attribute_id
                    from rec,
                         attributes
                    INNER JOIN  element_type_hordes h ON h.horde_attribute_id = attributes.id AND h.horde_type_id = $type_id
                    where attributes.id = rec.parent_attribute_id)

            SELECT id, attribute_name,parent_attribute_id
            FROM rec
            ;
            "
        );

        $ids = [];
        foreach ($res as $row) {
            $ids[] = $row->id;
        }
        /**
         * @var Builder $builder
         */
        $builder =  Attribute::whereIn('id',$ids);
        return $builder;
    }

//todo put in originating_horde_id for when the attribute is inherited, the element values will put that one in instead in the element_horde_id so bounds and stuff easily checked

}
