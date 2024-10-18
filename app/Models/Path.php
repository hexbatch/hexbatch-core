<?php

namespace App\Models;





use App\Enums\Paths\TypeOfPathStatus;
use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\ResourceNameReq;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * paths have cursors in full thing collections, this is stuck on at the end of the compiled sql
 * // design status can be run in tests with rules, when publishing rebuild paths
 * // paths with error or sabotaged will return false when run in things, making the thing row return false to its parent (or throw exception?)
 * //todo path part has trigger when deleted that will mark its parent as sabotaged unless in design mode, in which case is ok
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int path_owning_namespace_id
 * @property int path_handle_element_id
 * @property string ref_uuid

 * @property string path_name
 * @property string path_compiled_sql
 * @property TypeOfPathStatus path_status
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property UserNamespace namespace_owner
 * @property Element path_handle
 * @property PathPart path_root_part
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
        'path_status' => TypeOfPathStatus::class,
    ];


    public function namespace_owner() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'path_owning_namespace_id');
    }


    public function path_handle() : BelongsTo {
        return $this->belongsTo(Element::class,'path_handle_element_id');
    }

    public function path_root_part() : HasOne {
        return $this->hasOne(PathPart::class,'owning_path_id')
            /** @uses PathPart::path_part_children() */
            ->with('path_part_children')
            ->whereNull('parent_path_part_id');
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
                } else if (is_string($value)) {
                    $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);
                    if (count($parts) === 2) {
                        $owner_hint = $parts[0];
                        $maybe_name = $parts[1];
                        /**
                         * @var UserNamespace $owner
                         */
                        $owner = (new UserNamespace())->resolveRouteBinding($owner_hint);
                        $build = $this->where('path_owning_namespace_id', $owner?->id)->where('path_name', $maybe_name);
                    }
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
        ?int $owner_namespace_id = null
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


        /**
         * @uses Path::namespace_owner(),Path::path_handle(),Path::path_root_part()
         */
        $build->with('namespace_owner','path_handle','path_root_part');

        return $build;
    }

    public function getName() : string  {
        return $this->namespace_owner->getName() .UserNamespace::NAMESPACE_SEPERATOR. $this->path_name;
    }

    public function isInUse() : bool {

        //if it is used in a rule
        if (AttributeRule::where('rule_path_id',$this->id)->count() ) {return true;}


        //and cannot delete if in a path used by a thing
        if (PathPart::buildPathPart(pending_thing_type_id: $this->id)->exists() ) { return true;}
        return false;
    }

    public static function collectPath(Collection|string $collect,?UserNamespace $owner = null,?Path $path = null ) : Path {


        try {
            DB::beginTransaction();
            if (is_string($collect) && Utilities::is_uuid($collect)) {
                /**
                 * @var Path
                 */
                return (new Path())->resolveRouteBinding($collect);
            } else {

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
                if ($owner) {
                    $path->path_owning_namespace_id = $owner->id;
                }
                $path->editPath($collect);
            }

            DB::commit();

            return Path::buildPath(id:$path->id)->first();
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
    public function editPath(Collection $collect) : void {

        try {

            DB::beginTransaction();


            if ($collect->has('path_name')) {
                $this->path_name = $collect->get('path_name');
                try {
                    Validator::make(['path_name' => $this->path_name], [
                        'path_name' => ['required', 'string', new ResourceNameReq],
                    ])->validate();
                } catch (ValidationException $v) {
                    throw new HexbatchNotPossibleException($v->getMessage(),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::PATH_BAD_NAME);
                }
            }


            if (!$this->path_name) {
                throw new HexbatchNotPossibleException(
                    __('msg.path_needs_name'),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::PATH_BAD_NAME);
            }

            if ($collect->has('path_handle')) {
                $tree_element_hint = $collect->get('path_handle');
                if (is_string($tree_element_hint)) {
                    /** @var Element $path_tree_mark */
                    $path_tree_mark = (new Path())->resolveRouteBinding($tree_element_hint);
                    if (!$path_tree_mark->element_namespace->isNamespaceAdmin(Utilities::getCurrentNamespace())) {
                        throw new HexbatchNotPossibleException(
                            __('msg.path_tree_element_permissions', ['ref' => $path_tree_mark->getName()]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::PATH_SCHEMA_ISSUE);
                    }
                    $this->path_handle_element_id = $path_tree_mark->id;
                }
            }

            if (!$this->isInUse()) {

                if ($collect->has('parts')) {
                    collect($collect->get('parts'))->each(function ($hint_child, int $key) {
                        Utilities::ignoreVar($key);
                        PathPart::collectPathPart(collect: $hint_child,owner: $this);
                    });
                }


            }

            try {
                $this->save();
            } catch (\Exception $f) {
                throw new HexbatchNotPossibleException(
                    __('msg.path_cannot_be_edited',['ref'=>$this->getName(),'error'=>$f->getMessage()]),
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
