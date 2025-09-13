<?php

namespace App\Models;


use App\Enums\Types\TypeOfApproval;

use App\Enums\Types\TypeOfLifecycle;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int child_type_id
 * @property int parent_type_id
 * @property int parent_rank
 * @property TypeOfApproval parent_type_approval
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property ElementType parent_type
 *
 *
 */
class ElementTypeParent extends Model
{

    protected $table = 'element_type_parents';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'child_type_id',
        'parent_type_id',
        'parent_rank',
        'approval',
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
        'parent_type_approval' => TypeOfApproval::class
    ];

    public function parent_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'parent_type_id','id');
    }

    public function getName() :string {
        return $this->ref_uuid;
    }

    public static function updateParentStatus(ElementType $parent, ElementType $child,TypeOfApproval $approval)
    {
        /** @var static $current */
        $current = ElementTypeParent::buildTypeParents(child_type_id: $child->id,parent_type_id: $parent->id)->first();
        if (!$current) {
            throw new \InvalidArgumentException(sprintf("Parent child relationship not found for %s->%s ",$parent->ref_uuid,$child->ref_uuid ));
        }
        $current->parent_type_approval = $approval;
        $current->save();
    }

    /**
     * @throws \Exception
     */
    public static function addOrUpdateParent(ElementType $parent, ElementType $child, TypeOfApproval $approval = TypeOfApproval::PENDING_DESIGN_APPROVAL
        , bool                                           $check_parent_published = true)
    :ElementTypeParent
    {


        //parent is not checked for validity until the publish event
        try {
            DB::beginTransaction();
            if ( $parent->is_final_type) {
                throw new HexbatchNotPossibleException(__('msg.parent_type_is_not_inheritable',['ref'=>$parent->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::TYPE_CANNOT_INHERIT);
            }

            if ($check_parent_published) {
                if ($parent->lifecycle !== TypeOfLifecycle::PUBLISHED) {
                    throw new HexbatchNotPossibleException(__('msg.parent_type_must_be_published',['ref'=>$parent->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::TYPE_CANNOT_INHERIT);
                }
            }

            $par = new ElementTypeParent();

            $current_step = ElementTypeParent::where('child_type_id', $child->id)
                ->where('parent_type_id', $parent->id)
                ->max('parent_rank') ?? 0;

            if (!$current_step) {
                $current_step = ElementTypeParent::where('child_type_id', $child->id)->max('parent_rank') ?? 0;
            }
            //will check when the type is published
            $par->upsert([
                'child_type_id' => $child->id,
                'parent_type_id' => $parent->id,
                'parent_type_approval' => $approval,
                'parent_rank' => $current_step + 1,
            ], ['parent_type_id', 'child_type_id']);




            DB::commit();
            return $par;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public static function buildTypeParents(
        ?int    $me_id = null,
        ?string $uuid = null,
        ?int $child_type_id = null,
        ?int $parent_type_id = null,
        array $parent_ids = []

    ): Builder
    {

        /**
         * @var Builder $build
         */
        $build = ElementTypeParent::select('element_type_parents.*')
            ->selectRaw(" extract(epoch from  element_type_parents.created_at) as created_at_ts")
            ->selectRaw("extract(epoch from  element_type_parents.updated_at) as updated_at_ts")
        ;

        if ($me_id) {
            $build->where('element_type_parents.id', $me_id);
        }

        if ($uuid) {
            $build->where('element_type_parents.ref_uuid', $uuid);
        }

        if ($child_type_id) {
            $build->where('element_type_parents.child_type_id', $child_type_id);
        }

        if ($parent_type_id) {
            $build->where('element_type_parents.parent_type_id', $parent_type_id);
        }

        if (count($parent_ids)) {
            $build->whereIn('element_type_parents.parent_type_id', $parent_ids);
        }


        return $build;
    }



}
