<?php

namespace App\Models;


use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Sys\Res\Sets\ISet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_set_element_id
 * @property bool has_events
 * @property string ref_uuid
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property ElementSetMember[] element_members
 *
 */
class ElementSet extends Model implements ISet
{

    /*
     * sets always stay on the originating server, they can be copied to others
     *
When a parent is destroyed, its children, leafs first, are destroyed in a way that the children are done first.
Elements are updated here when the set is destroyed, unless the operation prevents this

It is possible to destroy a child set without this data merge.

Parent children can do unlimited nesting, but a child can never be a parent to the parents above it.


     */
    protected $table = 'element_sets';
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

    public function element_members() : HasMany {
        return $this->hasMany(ElementSetMember::class,'holder_set_id');
    }

    public static function buildSet(
        ?int $id = null
    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = Element::select('element_sets.*')
            ->selectRaw(" extract(epoch from  element_sets.created_at) as created_at_ts,  extract(epoch from  element_sets.updated_at) as updated_at_ts")
        ;

        if ($id) {
            $build->where('element_sets.id', $id);
        }

        /** @uses ElementSet::element_members(),ElementSetMember::of_element() */
        $build->with('element_members','element_members.of_element');

        return $build;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $build = null;
        $ret = null;
        $first_id = null;
        try {
            if ($field) {
                $build = $this->where($field, $value);
            } else {
                if (Utilities::is_uuid($value)) {
                    $build = $this->where('ref_uuid', $value);
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = ElementSet::buildSet(id:$first_id)->first();
                }
            }
        } finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.set_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::SET_NOT_FOUND
                );
            }
        }
        return $ret;

    }


    public function getSetObject(): ?ElementSet
    {
        return $this;
    }
}
