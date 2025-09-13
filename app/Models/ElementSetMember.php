<?php

namespace App\Models;


use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


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
    protected $casts = [];

    public function of_element() : BelongsTo {
        return $this->belongsTo(Element::class,'member_element_id');
    }




    public static function buildSetMember(
        ?int $id = null,
        ?int $set_id = null,
        ?int $element_id = null,
    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = Element::select('element_set_members.*');

        if ($id) {
            $build->where('element_set_members.id', $id);
        }

        if ($element_id) {
            $build->where('element_set_members.member_element_id', $element_id);
        }

        if ($set_id) {
            $build->where('element_set_members.holder_set_id', $set_id);
        }

        /**
         * @uses ElementSetMember::of_element()
         */
        $build->with('of_element');

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
