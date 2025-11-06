<?php

namespace App\Models;


use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\JoinClause;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int holder_set_id
 * @property int member_element_id
 * @property int member_rank
 * @property bool is_sticky
 *
 * @property Element of_element
 */
class ElementSetMember extends Model
{

    protected $table = 'element_set_members';
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function of_element() : BelongsTo {
        return $this->belongsTo(Element::class,'member_element_id');
    }

    public function hosting_intersection_state_changes() : HasManyThrough {
        return $this->hasManyThrough(
            SetMemberIntersectionChanges::class, //what is returned
            ElementTypeIntersection::class, //the connecting class
            'type_intersection_set_member_id', // Foreign key on the connecting table...
            'id', // Foreign key on the returned table...
            'id', // Local key on this class table...
            'hosting_intersection_id' // Local key on the connecting table...
        );
    }


    public function moved_intersection_state_changes() : HasManyThrough {
        return $this->hasManyThrough(
            SetMemberIntersectionChanges::class, //what is returned
            ElementTypeIntersection::class, //the connecting class
            'type_intersection_set_member_id', // Foreign key on the connecting table...
            'id', // Foreign key on the returned table...
            'id', // Local key on this class table...
            'moved_intersection_id' // Local key on the connecting table...
        );
    }




    public static function buildSetMember(
        ?int $id = null,
        ?int $set_id = null,
        ?string $set_ref = null,
        ?int $element_id = null,
        ?string $element_ref = null,
        array $element_ids = [],
        bool $b_relationship_element = true,
        bool $b_relationship_element_type = false,
        array   $given_ids = [],
    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = ElementSetMember::select('element_set_members.*');

        if ($id) {
            $build->where('element_set_members.id', $id);
        }

        if (count($given_ids)) {
            $build->whereIn('element_set_members.id', $given_ids);
        }


        if ($element_ref) {
            $build->where('element_set_members.ref_uuid', $element_ref);
        }

        if ($element_id) {
            $build->where('element_set_members.member_element_id', $element_id);
        }

        if (count($element_ids)) {
            $build->whereIn('element_set_members.member_element_id', $element_ids);
        }

        if ($set_id) {
            $build->where('element_set_members.holder_set_id', $set_id);
        }
        if ($set_ref) {
            $build->join('element_sets as s',
                /** @param JoinClause $join */
                function (JoinClause $join) use($set_ref) {
                    $join->on('element_set_members.holder_set_id', '=', 's.id')
                    ->where('s.ref_uuid',$set_ref);
                }
            );
        }

        if ($b_relationship_element) {
            /**
             * @uses ElementSetMember::of_element()
             */
            $build->with('of_element');
        }

        if ($b_relationship_element_type) {
            /**
             * @uses Element::element_parent_type()
             */
            $build->with('of_element.element_parent_type');
        }


        return $build;
    }

    public static function getMember(
        ElementSet $set,
        Element $element,
    )
    : ElementSetMember
    {
        /** @var ?ElementSetMember $ret */
        $ret = static::buildSetMember(set_id:$set->id,element_id: $element->id)->first();

        if (!$ret) {
            throw new HexbatchNotFound(
                __('msg.set_does_not_have_element',['ref'=>$set->getName(),'ele'=>$element->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::ELEMENT_NOT_IN_SET
            );
        }
        return $ret;
    }


}
