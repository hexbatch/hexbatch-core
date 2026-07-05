<?php

namespace App\Models;

use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Sys\Res\ISystemModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;

/*
 * Element destruction has two different modes
 * normal: an element can be requested to be destroyed, going through the event handlers, handlers can stop, elements in pending things keep from being destroyed
 * force :
 *      if in rules the branches are pruned to be false to parent
 *      if in any path parts, that path is made invalid and the element is nulled out, but otherwise the path is unchanged
 *      elements removed from all sets without event,
 *      if it is defining a set, that set is destroyed and the contents are popped out, but no events raised
 *      if defining a description, it is removed from resources,
 */

/**
 * @mixin Builder
 * @mixin QueryBuilder
 * @property int id
 * @property int element_parent_type_id
 * @property int element_phase_id
 * @property int element_namespace_id
 * @property bool is_system
 * @property string ref_uuid
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property UserNamespace element_namespace
 * @property ElementType element_parent_type
 * @property Phase element_phase
 */
class Element extends Model implements ISystemModel
{

    /*
     * elements always stay on the originating server, but they can be copied
     * only published types can make elements
     */
    protected $table = 'elements';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'element_parent_type_id',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


    public function element_namespace() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'element_namespace_id');
    }
    public function element_parent_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'element_parent_type_id');
    }

    public function element_phase() : BelongsTo {
        return $this->belongsTo(Phase::class,'element_phase_id');
    }

    public function linked_sets() : HasMany {
        return $this->hasMany(ElementLink::class,'linker_element_id','id')
            ->with('linked_set','linked_set.defining_element');
    }


    public static function buildElement(
        ?int    $me_id = null,
        array    $me_ids = [],
        ?int    $type_id = null,
        ?int    $attribute_id = null,
        ?int    $shape_id = null,
        ?int    $schedule_id = null,
        ?int    $set_id = null,
        ?int    $phase_id = null,
        ?int    $namespace_id = null,
        array   $in_namespace_ids = [],
        ?bool   $is_set = null,
        ?string $uuid = null,
        array   $given_uuids = [],
        ?string $set_ref = null,
        ?string $phase_ref = null,
        ?string $type_ref = null,
        ?string $namespace_ref = null,
        ?string $exposed_attribute_ref = null,
        ?string $included_attribute_ref = null,
        bool    $b_do_namespace_relation = false,
        bool    $b_do_namespace_type_relation = false,
        bool    $b_do_type_relation = false,
        bool    $b_do_link_relation = false,
        ?int    $not_member_set_id = null,
        bool    $b_use_select = true,
        bool    $b_check_visiblity = false

    ): Builder
    {

        /**
         * @var Builder $build
         */
        $build = Element::where('id', '<>',0);

        if ($b_use_select)
        {
            $build = $build->select('elements.*')
                ->selectRaw(" extract(epoch from  elements.created_at) as created_at_ts,  extract(epoch from  elements.updated_at) as updated_at_ts")
            ;
        }


        if ($me_id) {
            $build->where('elements.id', $me_id);
        }

        if (count($me_ids)) {
            $build->whereIn('elements.id', $me_ids);
        }

        if ($uuid) {
            $build->where('elements.ref_uuid', $uuid);
        }

        if (count($given_uuids)) {
            $build->whereIn('elements.ref_uuid', $given_uuids);
        }

        if ($phase_id) {
            $build->where('elements.element_phase_id', $phase_id);
        }

        if ($phase_ref) {
            $build->join('phases p',
                /** @param JoinClause $join */
                function (JoinClause $join) use ($phase_ref) {
                    $join->on('p.id', '=', 'elements.element_phase_id')
                        ->where('p.ref_uuid', $phase_ref);
                }
            );
        }

        if ($namespace_id) {
            $build->where('elements.element_namespace_id', $namespace_id);
        }

        if (count($in_namespace_ids)) {
            $build->whereIn('elements.element_namespace_id', $in_namespace_ids);
        }


        if ($namespace_ref) {
            $build->join('user_namespaces nee',
                /** @param JoinClause $join */
                function (JoinClause $join) use ($namespace_ref) {
                    $join->on('nee.id', '=', 'elements.element_namespace_id')
                        ->where('nee.ref_uuid', $namespace_ref);
                }
            );
        }

        if ($not_member_set_id) {
            $build->leftJoin('element_set_members setex',
                /** @param JoinClause $join */
                function (JoinClause $join) use ($not_member_set_id) {
                    $join->on('setex.member_element_id', '=', 'elements.id')
                        ->where('setex.holder_set_id', $not_member_set_id)
                        ->whereNull('setex.id')
                    ;
                }
            );
        }




        if($type_id) {
            $build->where('elements.element_parent_type_id', $type_id);
        }


        if ($type_ref) {
            $build->join('element_types tee',
                /** @param JoinClause $join */
                function (JoinClause $join) use ($type_ref) {
                    $join->on('nee.id', '=', 'elements.element_parent_type_id')
                        ->where('nee.ref_uuid', $type_ref);
                }
            );
        }

        if ($is_set !== null) {
            if ($is_set) {
                $build->join('element_sets s',
                    /** @param JoinClause $join */
                    function (JoinClause $join)  {
                        $join->on('s.parent_set_element_id', '=', 'elements.id');
                    }
                );
            } else {
                $build->leftJoin('element_sets s',
                    /** @param JoinClause $join */
                    function (JoinClause $join)  {
                        $join->on('s.parent_set_element_id', '=', 'elements.id');
                    }
                )->whereNull('s.id');
            }
        }

        if ($schedule_id) {
            $build->join('element_types aet',
                /** @param JoinClause $join */
                function (JoinClause $join) use ($schedule_id) {
                    $join->on('aet.id', '=', 'elements.element_parent_type_id')
                        ->where('aet.type_time_bound_id', $schedule_id);
                }
            );
        }

        if ($set_id) {
            $build->join('element_set_members sem',
                /** @param JoinClause $join */
                function (JoinClause $join) use ($set_id) {
                    $join->on('sem.member_element_id', '=', 'elements.id')
                        ->where('sem.id', $set_id);
                }
            );
        }

        if ($set_ref) {
            $build->join('element_set_members semp',
                /** @param JoinClause $join */
                function (JoinClause $join)  {
                    $join->on('semp.member_element_id', '=', 'elements.id');
                }
            );

            $build->join('element_sets sempe',
                /** @param JoinClause $join */
                function (JoinClause $join) use ($set_ref) {
                    $join->on('semp.holder_set_id', '=', 'sempe.id')
                        ->where('sem.ref_uuid', $set_ref);
                }
            );
        }


        if ($b_check_visiblity) {


            if ($set_id) {
                $build->leftJoin('element_visibilities el_vis',
                    /** @param JoinClause $join */
                    function (JoinClause $join) use($set_id,$set_ref) {
                        $join->on('el_vis.visible_element_id', '=', 'elements.id')
                            ->where('sem.id','=','el_vis.visible_set_member_id')
                            ->orWhere('el_vis.is_visible',true);
                    }
                );
            } elseif ($set_ref) {
                $build->leftJoin('element_visibilities el_vis',
                    /** @param JoinClause $join */
                    function (JoinClause $join) use($set_id,$set_ref) {
                        $join->on('el_vis.visible_element_id', '=', 'elements.id')
                            ->where('semp.id','=','el_vis.visible_set_member_id')
                            ->orWhere('el_vis.is_visible',true);
                    }
                );
            } else {
                $build->leftJoin('element_visibilities el_vis',
                    /** @param JoinClause $join */
                    function (JoinClause $join) use($set_id,$set_ref) {
                        $join->on('el_vis.visible_element_id', '=', 'elements.id')
                            ->whereNull('el_vis.visible_set_member_id')
                            ->orWhere('el_vis.is_visible',true);
                    }
                );

            }

            $build->whereNull('el_vis.id');

        }

        if ($attribute_id) {
            $build->join('attributes att',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($attribute_id) {
                    $join
                        ->on('elements.element_parent_type_id','=','att.owner_element_type_id')
                        ->where('att.id',$attribute_id);
                }
            );
        }

        if ($exposed_attribute_ref) {
            $build->join('element_type_exposed_attributes att_exposed',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('elements.element_parent_type_id','=','att_exposed.exposed_type_id');
                }
            );

            $build->join('attributes att_ref',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($exposed_attribute_ref) {
                    $join
                        ->on('att_exposed.exposed_attribute_id','=','att_ref.id')
                        ->where('att.ref_uuid',$exposed_attribute_ref);
                }
            );
        }

        if ($included_attribute_ref) {
            $build->join('element_type_included_attributes att_included',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('elements.element_parent_type_id','=','att_included.included_type_id');
                }
            );

            $build->join('attributes att_inc_ref',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($exposed_attribute_ref) {
                    $join
                        ->on('att_inc_ref.included_attribute_id','=','att_inc_ref.id')
                        ->where('att.ref_uuid',$exposed_attribute_ref);
                }
            );
        }

        if ($shape_id) {
            $build->join('attributes satt',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($shape_id) {
                    $join
                        ->on('elements.element_parent_type_id','=','satt.owner_element_type_id')
                        ->where('satt.attribute_shape_id',$shape_id);
                }
            );
        }


        if ($b_do_namespace_relation) {
            /** @uses Element::element_namespace() */
            $build->with('element_namespace');
        }

        if ($b_do_namespace_type_relation) {
            /** @uses Element::element_namespace(),UserNamespace::namespace_base_type() */
            $build->with('element_namespace.namespace_base_type');
        }

        if ($b_do_type_relation) {
            /** @uses Element::element_parent_type() */
            $build->with('element_parent_type');
        }

        if ($b_do_link_relation) {
            /** @uses Element::linked_sets() */
            $build->with('linked_sets');
        }



        return $build;
    }

    /**
     * @param string[]|\Illuminate\Support\Collection $values
     * @param bool $throw_exception
     * @return Collection|Element[]
     */
    public static function resolveElements( $values, bool $throw_exception = true)
    {

        $refs = [];
        foreach ($values as $val) {
            if (!Utilities::is_uuid($val)) {
                if ($throw_exception) {
                    throw new HexbatchNotFound(
                        __('msg.element_not_found',['ref'=>$val]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                        RefCodes::ELEMENT_NOT_FOUND
                    );
                } else {
                    continue;
                }
            }

            $refs[] = $val;

        }

       /** @var Collection|Element[] $ret */
        $ret = static::buildElement(given_uuids:$refs)->get();

        if (count($ret) !== count($values) ) {
            if ($throw_exception) {
                throw new HexbatchNotFound(
                    __('msg.element_list_not_found',['ref'=>implode('|',$values)]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::ELEMENT_NOT_FOUND
                );
            }
        }

        return $ret;
    }


    public static function getThisElement(
        ?int             $id = null,
        ?string          $uuid = null
    )
    : Element
    {
        $ret = static::buildElement(me_id:$id,uuid: $uuid)->first();

        if (!$ret) {
            $arg_types = []; $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($uuid) { $arg_types[] = 'uuid'; $arg_vals[] = $uuid;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new HexbatchNotFound(
                __('msg.element_not_found_by',['types'=>$arg_type,'values'=>$arg_val]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::ELEMENT_NOT_FOUND
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
        if (Utilities::is_uuid($value)) {
            return static::getThisElement(uuid: $value);
        }

        throw new HexbatchNotFound(
            __('msg.element_not_found',['ref'=>$value]),
            \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
            RefCodes::ELEMENT_NOT_FOUND
        );

    }

    public function getName() :string {
        return $this->ref_uuid.' from '.$this->element_parent_type->getName();
    }



    public function getUuid(): string{
        return $this->ref_uuid;
    }

    const DEFAULT_ELEMENT_LIMIT = 100;

    /** @return \Illuminate\Pagination\CursorPaginator<Element>|\Illuminate\Support\Collection<Element> */
    public static function getElementsFromParams(SelectElementParamData $params,
                                          bool $b_ns_relations ,bool $b_type_relations,bool $b_ns_type_relations,
                                          ?int $not_member_set_id = null,?string $cursor = null
    ) {

        $builder =  static::getBuilderFromParams(params: $params, b_ns_relations: $b_ns_relations,
            b_type_relations: $b_type_relations, b_ns_type_relations: $b_ns_type_relations, not_member_set_id: $not_member_set_id
        );
        /** @type \Illuminate\Pagination\CursorPaginator|\Illuminate\Support\Collection */
        return $builder->cursorPaginate(perPage: config('hbc.pagination.default_element_limit'), cursor: $cursor);

    }



    public static function getBuilderFromParams(SelectElementParamData $params,
                                          bool $b_ns_relations ,bool $b_type_relations,bool $b_ns_type_relations,
                                          ?int $not_member_set_id = null,bool $b_link_relations = false
    )
    : Builder
    {
        return static::buildElement(
            given_uuids: $params->element_refs, set_ref: $params->set_ref, phase_ref: $params->phase_ref,
            type_ref: $params->type_ref, namespace_ref: $params->namespace_ref, exposed_attribute_ref: $params->attribute_ref,
            b_do_namespace_relation: $b_ns_relations, b_do_namespace_type_relation: $b_ns_type_relations,
            b_do_type_relation: $b_type_relations,b_do_link_relation: $b_link_relations,not_member_set_id: $not_member_set_id,b_check_visiblity: true
        );

    }


}
