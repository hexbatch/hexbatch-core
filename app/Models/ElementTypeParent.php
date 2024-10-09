<?php

namespace App\Models;


use App\Enums\Types\TypeOfApproval;
use App\Enums\Types\TypeOfParentRole;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int child_type_id
 * @property int parent_type_id
 * @property int parent_rank
 * @property TypeOfParentRole parent_role
 * @property TypeOfApproval parent_type_approval
 *
 * @property string created_at
 * @property string updated_at
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
        'parent_role',
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
        'parent_role' => TypeOfParentRole::class,
        'parent_type_approval' => TypeOfApproval::class
    ];


    public function getName() :string {
        return $this->ref_uuid;
    }


    /**
     * @throws \Exception
     */
    public static function addParent(ElementType $parent, ElementType $child) :ElementTypeParent {



        try {
            DB::beginTransaction();
            $user_namespace = Utilities::getCurrentNamespace();
            if ( $parent->is_final || !$parent->canNamespaceInherit($user_namespace)) {
                throw new HexbatchNotPossibleException(__('msg.parent_type_is_not_inheritable'),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::TYPE_CANNOT_INHERIT);
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
                'parent_rank' => $current_step + 1,
            ], ['parent_type_id', 'child_type_id']);


            //todo write to thing to add the attribute to the type if the approval is not automatic, then send this to things
            // type construction (except user tokens) takes place in the owning namespace's home set,

            DB::commit();
            return $par;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }



}
