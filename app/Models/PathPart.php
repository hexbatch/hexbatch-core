<?php

namespace App\Models;



use App\Enums\Paths\PathRelationshipType;
use App\Enums\Paths\PathReturnsType;
use App\Enums\Paths\TimeComparisonType;

use App\Enums\Rules\TypeOfLogic;

use App\Enums\Types\TypeOfLifecycle;
use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\ResourceNameReq;
use ArrayObject;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owning_path_id
 * @property int parent_path_part_id
 * @property int path_type_id
 * @property int path_server_id
 * @property int path_attribute_id
 * @property int sorting_attribute_id
 * @property int path_element_set_id
 * @property int path_element_id
 * @property int path_namespace_id
 * @property int path_map_bound_id
 * @property int path_shape_bound_id
 * @property int path_time_bound_id
 * @property int path_min_gap
 * @property int path_max_gap
 * @property int path_min_count
 * @property int path_max_count
 * @property int path_result_limit
 * @property bool is_partial_matching_name
 * @property bool is_sorting_order_asc
 * @property string ref_uuid
 * @property string path_start_at
 * @property string path_end_at
 * @property string path_part_name
 * @property string filter_json_path
 * @property string sort_json_path
 * @property string path_part_compiled_sql
 *
 * @property ArrayObject path_shape_geo_json
 * @property ArrayObject path_map_geo_json
 *
 * @property TypeOfLogic path_child_logic
 * @property TypeOfLogic path_logic
 * @property PathRelationshipType path_relationship
 * @property TimeComparisonType time_comparison
 * @property PathReturnsType path_returns
 * @property TypeOfLifecycle path_lifecycle
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property UserNamespace path_owner
 * @property PathPart path_part_parent
 * @property PathPart[] path_part_children
 * @property Element path_tree_element
 */
class PathPart extends Model
{

    protected $table = 'path_parts';
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
        'path_relationship' => PathRelationshipType::class,
        'time_comparison' => TimeComparisonType::class,
        'path_returns' => PathReturnsType::class,
        'path_child_logic' => TypeOfLogic::class,
        'path_logic' => TypeOfLogic::class,
        'path_lifecycle' => TypeOfLifecycle::class,
        'path_shape_geo_json' => AsArrayObject::class,
        'path_map_geo_json' => AsArrayObject::class,
    ];


    public function path_owner() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'owning_path_id');
    }

    public function path_part_parent() : BelongsTo {
        return $this->belongsTo(PathPart::class,'parent_path_part_id');
    }

    public function path_part_children() : HasMany {
        return $this->hasMany(PathPart::class,'parent_path_part_id')
            /** @uses PathPart::path_part_children() */
            ->with('path_part_children');
    }




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
                } else {
                    if (is_string($value)) {
                        $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);
                        if (count($parts) === 3) {
                            $namespace_hint = $parts[0];
                            $path_hint = $parts[0];
                            $part_name = $parts[1];

                            /** @var Path $path */
                            $path = (new Path())->resolveRouteBinding($namespace_hint . UserNamespace::NAMESPACE_SEPERATOR . $path_hint);

                            $build = $this->where('owning_path_id', $path->id)->where('path_part_name', $part_name);
                        }
                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = PathPart::buildPathPart(id:$first_id)->first();
                }
            }
        }
        catch (\Exception $e) {
            Log::warning('PathPart resolving: '. $e->getMessage());
        }
        finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.path_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::PATH_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    public static function buildPathPart(
        ?int $id = null,
        ?int $owner_path_id = null,
        ?int $type_id = null,
        ?int $pending_thing_type_id = null
    )
    : Builder
    {

        /** @var Builder $build */
        $build = PathPart::select('path_parts.*')
            ->selectRaw(" extract(epoch from  path_parts.created_at) as created_at_ts,  extract(epoch from  path_parts.updated_at) as updated_at_ts")
        ;

        if ($id) {
            $build->where('path_parts.id', $id);
        }
        if ($owner_path_id) {
            $build->where('path_parts.owning_path_id', $owner_path_id);
        }

        if ($pending_thing_type_id) {
            $build->join('paths',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('paths.id','=','paths.owning_path_id');
                }
            );

            // join to the thing table that is pending, then find if using the type anywhere
            $build->join('things',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('path_parts.id','=','things.thing_path_id');
                }
            );
        }

        if ($type_id || $pending_thing_type_id) {
            $build->where('path_parts.path_type_id', $type_id);
        }

        /**
         * @uses PathPart::path_owner(),PathPart::path_part_parent(),PathPart::path_part_children()
         */
        $build->with('path_owner','path_part_parent','path_part_children');

        return $build;
    }

    public function getName(bool $short_name = true) : string  {

        if ($short_name) {
            return $this->path_part_name;
        }
        //get ancestor chain
        $names = [];
        $parent = $this->path_part_parent;
        while ($parent) {
            $names[] = $parent->getName();
            $parent = $parent->path_part_parent;

        }
        if (empty($names)) {
            return $this->getName();
        }

        $detail =   implode(Attribute::ATTRIBUTE_FAMILY_SEPERATOR,$names);
        $root = '';
        if ($this->path_owner) { $root = $this->path_owner->getName() . UserNamespace::NAMESPACE_SEPERATOR ;}
        return $root. $detail;
    }


    public static function collectPathPart(Collection|string $collect, Path $owner , ?PathPart $parent = null, ?PathPart $part = null ) : PathPart {


        try {
            DB::beginTransaction();
            if (is_string($collect) && Utilities::is_uuid($collect)) {
                /**
                 * @var PathPart
                 */
                return (new PathPart())->resolveRouteBinding($collect);
            } else {
                if(!$part) {
                    if ($collect->has('uuid')) {
                        $maybe_uuid = $collect->get('uuid');
                        if (is_string($maybe_uuid) && Utilities::is_uuid($maybe_uuid)) {
                            /** @var PathPart $part */
                            $part = (new PathPart())->resolveRouteBinding($maybe_uuid);
                            if ($part->path_owner->ref_uuid !== $owner->ref_uuid) {
                                throw new \LogicException("Mismatch of path owner and passed in owner ");
                            }
                        } else {

                            throw new HexbatchNotFound(
                                __('msg.path_not_found', ['ref' => (string)$maybe_uuid]),
                                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                                RefCodes::PATH_NOT_FOUND
                            );
                        }
                    }
                } else {
                    $part = new PathPart();
                }

                if ($parent) {
                    if ($part?->owning_path_id !== $parent->owning_path_id) {
                        throw new HexbatchNotPossibleException(
                            __('msg.part_parent_is_on_different_tree', ['parent' => $parent->getName(),$part->getName()]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                            RefCodes::PATH_NOT_FOUND
                        );
                    }
                    $part->parent_path_part_id = $parent->id;
                }
                $part->owning_path_id = $parent->id;

                //check to make sure same rule chain


                $part->editPath($collect,$owner);
            }

            DB::commit();
            return $part;

        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof HexbatchCoreException) {
                throw $e;
            }
            throw new HexbatchNotPossibleException(
                $e->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);

        }
    }


    /**
     * @throws \Exception
     */
    public function editPath(Collection $collect, Path $owner) : void {

        try {

            DB::beginTransaction();


            if ($collect->has('part_name')) {
                $this->path_part_name = $collect->get('part_name');
                try {
                    Validator::make(['part_name' => $this->path_part_name], [
                        'part_name' => ['required', 'string', new ResourceNameReq],
                    ])->validate();
                } catch (ValidationException $v) {
                    throw new HexbatchNotPossibleException($v->getMessage(),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::PATH_BAD_NAME);
                }
            }


            if (!$this->path_part_name) {
                throw new HexbatchNotPossibleException(
                    __('msg.path_part_needs_name',['path'=>$owner->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::PATH_BAD_NAME);
            }


            if (!$owner->isInUse()) {
                if ($collect->has('parent')) {
                    $maybe_uuid = $collect->get('parent');
                    if (is_string($maybe_uuid)) {
                        /** @var PathPart $parent_path */
                        $parent_path = (new PathPart())->resolveRouteBinding($maybe_uuid);
                        $this->parent_path_part_id = $parent_path->id;

                    } else {
                        throw new HexbatchNotPossibleException(
                            __('msg.path_parent_not_found', ['ref' => $maybe_uuid]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::PATH_SCHEMA_ISSUE);
                    }
                }

                if ($collect->has('map_bound') || $collect->has('shape_bound') || $collect->has('time_bound') ) {
                    $owner_namespace = $owner->namespace_owner;
                    if (!$owner_namespace) {$owner_namespace = UserNamespace::buildNamespace(id: $owner->path_owning_namespace_id)->first();}

                    if ($collect->has('map_bound')) {

                        $hint_location_bound = $collect->get('location_bound');
                        if (is_string($hint_location_bound) || $hint_location_bound instanceof Collection) {
                            $bound = LocationBound::collectLocationBound(collect: $hint_location_bound,namespace: $owner_namespace);
                            $this->path_map_bound_id = $bound->id;
                        }
                    }

                    if ($collect->has('shape_bound')) {

                        $hint_location_bound = $collect->get('location_bound');
                        if (is_string($hint_location_bound) || $hint_location_bound instanceof Collection) {
                            $bound = LocationBound::collectLocationBound(collect: $hint_location_bound,namespace: $owner_namespace);
                            $this->path_shape_bound_id = $bound->id;
                        }
                    }

                    if ($collect->has('time_bound')) {
                        $hint_time_bound = $collect->get('time_bound');
                        if (is_string($hint_time_bound) || $hint_time_bound instanceof Collection) {
                            $bound = TimeBound::collectTimeBound(collect: $hint_time_bound,namespace: $owner_namespace);
                            $this->path_time_bound_id = $bound->id;
                        }
                    }

                }


                if ($collect->has('namespace')) {
                    $hint_namespace = $collect->get('namespace');
                    /** @var UserNamespace $namespace */
                    $namespace = (new UserNamespace())->resolveRouteBinding($hint_namespace);
                    $this->path_namespace_id = $namespace->id;
                }

                if ($collect->has('server')) {
                    $hint_server = $collect->get('server');
                    /** @var Server $server */
                    $server = (new Server())->resolveRouteBinding($hint_server);
                    $this->path_server_id = $server->id;
                }

                if ($collect->has('type')) {
                    $hint_type = $collect->get('type');
                    /** @var ElementType $type */
                    $type = (new ElementType())->resolveRouteBinding($hint_type);
                    $this->path_type_id = $type->id;
                }

                if ($collect->has('set')) {
                    $set_hint = $collect->get('set');
                    /** @var ElementSet $set */
                    $set = (new ElementSet())->resolveRouteBinding($set_hint);
                    $this->path_element_set_id = $set->id;
                }

                if ($collect->has('element')) {
                    $element_hint = $collect->get('element');
                    /** @var Element $element */
                    $element = (new Element())->resolveRouteBinding($element_hint);
                    $this->path_element_id = $element->id;
                }

                if ($collect->has('sorting')) {
                    $hint_sort = $collect->get('sorting');
                    /** @var Attribute $attr */
                    $attr = (new Attribute())->resolveRouteBinding($hint_sort);
                    $this->sorting_attribute_id = $attr->id;
                }

                if ($collect->has('attribute')) {
                    $attribute_type = $collect->get('attribute');
                    /** @var Attribute $sorter */
                    $sorter = (new Attribute())->resolveRouteBinding($attribute_type);
                    $this->path_attribute_id = $sorter->id;
                }


                if ($collect->has('is_partial_matching_name')) {
                    $this->is_partial_matching_name = Utilities::boolishToBool($collect->get('is_partial_matching_name', false));
                }

                if ($collect->has('is_sorting_order_asc')) {
                    $this->is_sorting_order_asc = Utilities::boolishToBool($collect->get('is_sorting_order_asc', false));
                }


                if ($collect->has('path_min_gap')) {
                    $this->path_min_gap = intval($collect->get('path_min_gap'));
                }

                if ($collect->has('path_max_gap')) {
                    $this->path_max_gap = intval($collect->get('path_max_gap'));
                }

                if ($collect->has('path_result_limit')) {
                    $this->path_result_limit = intval($collect->get('path_result_limit'));
                }

                if ($collect->has('path_min_count')) {
                    $this->path_min_count = intval($collect->get('path_min_count'));
                }

                if ($collect->has('path_max_count')) {
                    $this->path_max_count = intval($collect->get('path_max_count'));
                }


                if ($collect->has('path_start_at')) {
                    $this->path_start_at = Carbon::parse($collect->get('path_start_at'))->setTimezone(config('app.timezone'));
                }

                if ($collect->has('path_end_at')) {
                    $this->path_end_at = Carbon::parse($collect->get('path_end_at'))->setTimezone(config('app.timezone'));
                }


                if ($collect->has('filter_json_path')) {
                    $this->filter_json_path = $collect->get('filter_json_path');
                    Utilities::testValidJsonPath($this->filter_json_path);
                }

                if ($collect->has('sort_json_path')) {
                    $this->sort_json_path = $collect->get('sort_json_path');
                    Utilities::testValidJsonPath($this->sort_json_path);
                }

                if ($collect->has('shape_geo_json')) {
                    $what_geo = $collect->get('shape_geo_json');
                    if (is_array($what_geo)) {
                        $this->path_shape_geo_json = $what_geo;
                    }
                    if (empty($this->path_shape_geo_json)) {$this->path_shape_geo_json = null;}
                }

                if ($collect->has('map_geo_json')) {
                    $what_geo = $collect->get('map_geo_json');
                    if (is_array($what_geo)) {
                        $this->path_map_geo_json = $what_geo;
                    }
                    if (empty($this->path_map_geo_json)) {$this->path_map_geo_json = null;}
                }


                if ($collect->has('path_relationship')) {
                    $this->path_relationship = PathRelationshipType::tryFromInput($collect->get('path_relationship'));
                }

                if ($collect->has('time_comparison')) {
                    $this->time_comparison = TimeComparisonType::tryFromInput($collect->get('time_comparison'));
                }

                if ($collect->has('path_returns')) {
                    $this->path_returns = PathReturnsType::tryFromInput($collect->get('path_returns'));
                }

                if ($collect->has('path_child_logic')) {
                    $this->path_child_logic = TypeOfLogic::tryFromInput($collect->get('path_child_logic'));
                }

                if ($collect->has('path_logic')) {
                    $this->path_logic = TypeOfLogic::tryFromInput($collect->get('path_logic'));
                }
            } //end if not in use


            try {
                $this->save();
            } catch (\Exception $f) {
                throw new HexbatchNotPossibleException(
                    __('msg.attribute_cannot_be_edited',['ref'=>$this->getName(),'error'=>$f->getMessage()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }

            if ($collect->has('children')) {
                $myself_as_parent = Path::buildPath(id:$this->id)->first();
                collect($collect->get('children'))->each(function ($hint_child, int $key) use($myself_as_parent,$owner) {
                    Utilities::ignoreVar($key);
                    PathPart::collectPathPart(collect: $hint_child, owner: $owner, parent: $myself_as_parent);
                });
            }


            DB::commit();


        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function checkPartOwnership(Path $owner) {
        if ($this->id && $this->owning_path_id !== $owner->id) {

            throw new HexbatchNotFound(
                __('msg.path_owner_does_not_match_part_given',['ref'=>$this->getName(),'path'=>$owner->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::RULE_NOT_FOUND
            );
        }
    }

    public function delete_subtree() :void {
        if ($this->path_owner->isInUse()) {
            throw new HexbatchNotFound(
                __('msg.path_cannot_be_changed_if_in_use',['ref'=>$this->path_owner->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::PATH_NOT_FOUND
            );
        }
        try {
            DB::beginTransaction();
            $this->delete();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
