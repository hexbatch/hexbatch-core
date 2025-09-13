<?php

namespace App\Models;


use App\Exceptions\HexbatchDifferentPhase;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Sys\Res\ISystemModel;
use App\Sys\Res\Sets\ISet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\JoinClause;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_set_element_id
 * @property bool has_events
 * @property bool is_system
 * @property string ref_uuid
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property ElementSetMember[] element_members
 * @property Element defining_element
 * @property ElementSet parent_set
 * @property ElementSet[] children_sets
 *
 */
class ElementSet extends Model implements ISet,ISystemModel
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

    public function defining_element() : BelongsTo {
        return $this->belongsTo(Element::class,'parent_set_element_id');
    }

    public function children_sets() : HasMany {
        return $this->hasMany(ElementSetChild::class,'parent_set_id','id');
    }

    public function parent_set() : HasOne {
        return $this->hasOne(ElementSetChild::class,'child_set_id','id');
    }

    public static function buildSet(
        ?int            $me_id = null,
        ?string         $uuid = null,
        ?int            $parent_set_id = null,
        ?int            $type_id = null,
        ?int            $phase_id = null,
        ?int            $namespace_id = null,
        array           $in_namespace_ids = [],
        bool            $b_do_relations = false

    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = ElementSet::select('element_sets.*')
            ->selectRaw(" extract(epoch from  element_sets.created_at) as created_at_ts,  extract(epoch from  element_sets.updated_at) as updated_at_ts")
        ;

        if ($me_id) {
            $build->where('element_sets.id', $me_id);
        }

        if ($uuid) {
            $build->where('element_sets.ref_uuid', $uuid);
        }

        if ($parent_set_id ) {
            $build->join('element_set_members sim',
                /** @param JoinClause $join */
                function (JoinClause $join)  use($parent_set_id) {
                    $join->on('sim.child_set_id', '=', 'element_sets.id')
                        ->where('sim.parent_set_id',$parent_set_id);
                }
            );
        }

        if ($namespace_id ) {
            $build->join('elements e_one',
                /** @param JoinClause $join */
                function (JoinClause $join)  use($namespace_id) {
                    $join->on('e_one.id', '=', 'element_sets.parent_set_element_id')
                    ->where('e_one.element_namespace_id',$namespace_id);
                }
            );
        }

        if (count($in_namespace_ids) ) {
            $build->join('elements e_two',
                /** @param JoinClause $join */
                function (JoinClause $join)  use($in_namespace_ids) {
                    $join->on('e_two.id', '=', 'element_sets.parent_set_element_id')
                        ->whereIn('e_two.element_namespace_id',$in_namespace_ids);
                }
            );
        }

        if ($phase_id ) {
            $build->join('elements e_phase',
                /** @param JoinClause $join */
                function (JoinClause $join)  use($phase_id) {
                    $join->on('e_phase.id', '=', 'element_sets.parent_set_element_id')
                        ->whereIn('e_phase.element_phase_id',$phase_id);
                }
            );
        }

        if ($type_id ) {
            $build->join('elements e_type',
                /** @param JoinClause $join */
                function (JoinClause $join)  use($type_id) {
                    $join->on('e_type.id', '=', 'element_sets.parent_set_element_id')
                        ->where('e_type.element_parent_type_id',$type_id);
                }
            );
        }

        if ($b_do_relations) {
            /** @uses ElementSet::element_members(),ElementSet::defining_element(),ElementSetMember::of_element() */
            $build->with('element_members','defining_element','element_members.of_element');
        }


        return $build;
    }

    public static function resolveSet(string $value, bool $throw_exception = true)
    : static
    {

        /** @var Builder $build */
        $build = null;

        if (Utilities::is_uuid($value)) {
           return static::getThisSet(uuid: $value);
        }

        $ret = $build?->first();

        if (empty($ret) && $throw_exception) {
            throw new HexbatchNotFound(
                __('msg.set_not_found',['ref'=>$value]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::SET_NOT_FOUND
            );
        }

        return $ret;
    }

    public static function getThisSet(
        ?int             $id = null,
        ?string          $uuid = null
    )
    : ElementSet
    {
        $ret = static::buildSet(me_id:$id,uuid: $uuid)->first();

        if (!$ret) {
            $arg_types = []; $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($uuid) { $arg_types[] = 'uuid'; $arg_vals[] = $uuid;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new HexbatchNotFound(
                __('msg.set_not_found_by',['types'=>$arg_type,'values'=>$arg_val]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::SET_NOT_FOUND
            );
        }
        return $ret;
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
        return static::resolveSet($value);
    }


    public function getSetObject(): ?ElementSet {
        return $this;
    }

    public function getUuid(): string{
        return $this->ref_uuid;
    }



    public function addElement(Element $ele, bool $is_sticky = false) : ElementSetMember {
        // see if element is same phase as set
        if ($ele->element_phase_id !== $this->defining_element->element_phase_id) {
            throw new HexbatchDifferentPhase(__("msg.set_has_different_phase_than_element_entering",
                ['ref'=>$this->getName(),'ele'=>$ele->getName(),'set_phase'=>$this->defining_element->element_phase->getName(),
                    'ele_phase'=>$ele->element_phase->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::PHASE_IS_DIFFERENT);
        }
        $node = new ElementSetMember();
        $node->holder_set_id = $this->id;
        $node->member_element_id = $ele->id;
        $node->is_sticky = $is_sticky;
        $node->save();
        return $node;
    }

    public function getName(): string {
        return $this->ref_uuid.' from '.$this->defining_element->getName();
    }
}
