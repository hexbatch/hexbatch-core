<?php

namespace App\Models;



use App\Enums\Paths\PathRelationshipType;
use App\Enums\Paths\PathReturnsType;
use App\Enums\Paths\TimeComparisonType;

use App\Enums\Rules\TypeOfChildLogic;

use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\ResourceNameReq;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @property int path_owning_namespace_id
 * @property int path_tree_element_id
 * @property int parent_path_id
 * @property int path_type_id
 * @property int path_server_id
 * @property int path_attribute_id
 * @property int sorting_attribute_id
 * @property int path_element_set_id
 * @property int path_element_id
 * @property int path_namespace_id
 * @property int path_location_bound_id
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
 * @property string path_compiled_sql
 *
 * @property TypeOfChildLogic path_child_logic
 * @property TypeOfChildLogic path_logic
 * @property PathRelationshipType path_relationship
 * @property TimeComparisonType time_comparison
 * @property PathReturnsType path_returns
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property UserNamespace namespace_owner
 * @property Path path_parent
 * @property Element path_tree_element
 */
class Path extends Model
{

    protected $table = 'paths';
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
        'path_child_logic' => TypeOfChildLogic::class,
        'path_logic' => TypeOfChildLogic::class,
    ];


    public function namespace_owner() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'path_owning_namespace_id');
    }

    public function path_parent() : BelongsTo {
        return $this->belongsTo(Path::class,'parent_path_id');
    }

    public function path_tree_element() : BelongsTo {
        return $this->belongsTo(Element::class,'path_tree_element_id');
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
                    //the ref
                    $build = $this->where('ref_uuid', $value);
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = Path::buildPath(id:$first_id)->first();
                }
            }
        }
        catch (\Exception $e) {
            Log::warning('Path resolving: '. $e->getMessage());
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

    public static function buildPath(
        ?int $id = null,
        ?int $owner_namespace_id = null,
        ?int $type_id = null,
        ?int $pending_thing_type_id = null
    )
    : Builder
    {

        /** @var Builder $build */
        $build = Path::select('paths.*')
            ->selectRaw(" extract(epoch from  paths.created_at) as created_at_ts,  extract(epoch from  paths.updated_at) as updated_at_ts")
        ;

        if ($id) {
            $build->where('paths.id', $id);
        }
        if ($owner_namespace_id) {
            $build->where('paths.path_owning_namespace_id', $owner_namespace_id);
        }

        if ($pending_thing_type_id) {
            // join to the thing table that is pending, then find if using the type anywhere
            $build->join('things',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('paths.id','=','things.thing_path_id');
                }
            );
        }

        if ($type_id || $pending_thing_type_id) {
            $build->where('paths.path_type_id', $type_id);
        }

        /**
         * @uses Path::namespace_owner(),Path::path_parent(),Path::path_tree_element()
         */
        $build->with('namespace_owner','path_parent','path_tree_element');

        return $build;
    }

    public function getName(bool $short_name = true) : string  {

        if ($short_name) {
            return $this->path_part_name;
        }
        //get ancestor chain
        $names = [];
        $parent = $this->path_parent;
        while ($parent) {
            $names[] = $parent->getName();
            $parent = $parent->path_parent;

        }
        if (empty($names)) {
            return $this->getName();
        }

        $detail =   implode(Attribute::ATTRIBUTE_FAMILY_SEPERATOR,$names);
        $root = '';
        if ($this->namespace_owner) { $root = $this->namespace_owner . UserNamespace::NAMESPACE_SEPERATOR;}
        return $root. $detail;
    }

    public function isInUse() : bool {

        //if it is used in a rule
        if (AttributeRule::where('rule_path_id',$this->id)->count() ) {return true;}

        //if it is used as a bounds
        if (ElementType::where('type_bound_path_id',$this->id)->count() ) {return true;}

        //and cannot delete if in a path used by a thing
        if (Path::buildPath(pending_thing_type_id: $this->id)->exists() ) { return true;}
        return false;
    }

    public static function collectPath(Collection|string $collect,?UserNamespace $owner = null,?Path $path = null ) : Path {

        if (!$owner) {
            $owner = Utilities::getCurrentNamespace();
        }
        try {
            DB::beginTransaction();
            if (is_string($collect) && Utilities::is_uuid($collect)) {
                /**
                 * @var Path
                 */
                return (new Path())->resolveRouteBinding($collect);
            } else {
                if (!$owner->isNamespaceAdmin(Utilities::getCurrentNamespace())) {
                    throw new HexbatchNotFound(
                        __('msg.path_only_admin_can_edit',['ref'=>$path?->getName(),'ns'=>$owner->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                        RefCodes::PATH_CANNOT_EDIT
                    );
                }
                if(!$path) {
                    if ($collect->has('uuid')) {
                        $maybe_uuid = $collect->get('uuid');
                        if (is_string($maybe_uuid) && Utilities::is_uuid($maybe_uuid)) {
                            /** @var Path $path */
                            $path = (new Path())->resolveRouteBinding($maybe_uuid);
                            if ($path->namespace_owner->ref_uuid !== $owner->ref_uuid) {
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
                    $path = new Path();
                }

                $path->editPath($collect,$owner);
            }

            DB::commit();
            return $path;
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
    public function editPath(Collection $collect, UserNamespace $owner) : void {

        try {

            DB::beginTransaction();


            if ($collect->has('path_name')) {
                $this->path_part_name = $collect->get('path_name');
                try {
                    Validator::make(['path_name' => $this->path_part_name], [
                        'path_name' => ['required', 'string', new ResourceNameReq],
                    ])->validate();
                } catch (ValidationException $v) {
                    throw new HexbatchNotPossibleException($v->getMessage(),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::PATH_BAD_NAME);
                }
            }


            if (!$this->path_part_name) {
                $this->path_part_name = null;
            }

            if (!$this->isInUse()) {

                $this->path_owning_namespace_id = $owner->id;




                if ($collect->has('path_tree_element')) {
                    $tree_element_hint = $collect->get('path_tree_element');
                    if (is_string($tree_element_hint)) {
                        /** @var Element $path_tree_mark */
                        $path_tree_mark = (new Path())->resolveRouteBinding($tree_element_hint);
                        if (!$path_tree_mark->element_namespace->isNamespaceMember(Utilities::getCurrentNamespace())) {
                            throw new HexbatchNotPossibleException(
                                __('msg.path_tree_element_permissions', ['ref' => $path_tree_mark->getName()]),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::PATH_SCHEMA_ISSUE);
                        }
                        $this->path_tree_element_id = $path_tree_mark->id;
                    }
                }

                if (!$this->path_tree_element_id) {
                    throw new HexbatchNotPossibleException(
                        __('msg.path_tree_element_missing', ['ref' => $this->path_part_name]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::PATH_SCHEMA_ISSUE);
                }





                if ($collect->has('parent')) {
                    $maybe_uuid = $collect->get('parent');
                    if (is_string($maybe_uuid)) {
                        /** @var Path $parent_path */
                        $parent_path = (new Path())->resolveRouteBinding($maybe_uuid);
                        $this->parent_path_id = $parent_path->id;

                    } else {
                        throw new HexbatchNotPossibleException(
                            __('msg.path_parent_not_found', ['ref' => $maybe_uuid]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::PATH_SCHEMA_ISSUE);
                    }
                }


                if ($collect->has('location_bound')) {
                    $hint_location_bound = $collect->get('location_bound');
                    if (is_string($hint_location_bound) || $hint_location_bound instanceof Collection) {
                        $bound = LocationBound::collectLocationBound($hint_location_bound);
                        $this->path_location_bound_id = $bound->id;
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
                    $this->is_partial_matching_name = Utilities::boolishToBool($collect->get('is_partial_matching_name',false));
                }

                if ($collect->has('is_sorting_order_asc')) {
                    $this->is_sorting_order_asc = Utilities::boolishToBool($collect->get('is_sorting_order_asc',false));
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
                    $this->path_child_logic = TypeOfChildLogic::tryFromInput($collect->get('path_child_logic'));
                }

                if ($collect->has('path_logic')) {
                    $this->path_logic = TypeOfChildLogic::tryFromInput($collect->get('path_logic'));
                }

            }

            try {
                $this->save();
            } catch (\Exception $f) {
                throw new HexbatchNotPossibleException(
                    __('msg.attribute_cannot_be_edited',['ref'=>$this->getName(),'error'=>$f->getMessage()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }


            DB::commit();


        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
